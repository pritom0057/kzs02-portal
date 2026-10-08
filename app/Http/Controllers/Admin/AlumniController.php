<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNote;
use App\Models\Alumni;
use App\Models\PaymentLog;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $alumni = Alumni::where('role', 'alumni')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) =>
                    $q->where('name', 'like', "%$s%")
                      ->orWhere('roll_number', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%")
                )
            )
            ->withCount('adminNotes')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'pending'  => Alumni::where('role', 'alumni')->where('status', 'pending')->count(),
            'verified' => Alumni::where('role', 'alumni')->where('status', 'verified')->count(),
            'rejected' => Alumni::where('role', 'alumni')->where('status', 'rejected')->count(),
            'all'      => Alumni::where('role', 'alumni')->count(),
        ];

        return view('admin.alumni.index', compact('alumni', 'status', 'counts'));
    }

    public function show(Alumni $alumnus)
    {
        $alumnus->load(['eventRegistration', 'adminNotes.admin', 'paymentLogs']);

        return view('admin.alumni.show', compact('alumnus'));
    }

    public function verify(Alumni $alumnus)
    {
        $alumnus->update(['status' => 'verified']);

        return back()->with('success', "{$alumnus->name} has been verified.");
    }

    public function reject(Alumni $alumnus)
    {
        $alumnus->update(['status' => 'rejected']);

        return back()->with('success', "{$alumnus->name} has been rejected.");
    }

    public function confirmPayment(Alumni $alumnus)
    {
        $reg = $alumnus->eventRegistration;
        if (!$reg) return back()->with('error', 'No event registration found.');

        $previouslyPaid  = $reg->paid_amount ?? 0;
        $amountConfirmed = $reg->total_amount - $previouslyPaid;
        $admin           = auth()->user()->name;

        $reg->update([
            'payment_status' => 'paid',
            'paid_amount'    => $reg->total_amount,
        ]);

        $label = $previouslyPaid > 0 ? 'Additional payment confirmed' : 'Payment confirmed';
        PaymentLog::create([
            'alumni_id' => $alumnus->id,
            'type'      => 'confirmed',
            'amount'    => max(0, $amountConfirmed),
            'method'    => $reg->payment_method,
            'reference' => $reg->payment_reference,
            'note'      => "{$label} — ৳" . number_format(max(0, $amountConfirmed)) . " via " . strtoupper($reg->payment_method ?? 'manual') . ". Total paid: ৳" . number_format($reg->total_amount),
            'actor'     => $admin,
        ]);

        return back()->with('success', "{$alumnus->name}'s payment confirmed — ৳" . number_format($reg->total_amount) . " fully paid.");
    }

    public function resetPayment(Alumni $alumnus)
    {
        $reg = $alumnus->eventRegistration;
        if (!$reg) return back()->with('error', 'No event registration found.');

        $previousPaid = $reg->paid_amount ?? 0;

        $reg->update([
            'payment_status' => 'unpaid',
            'paid_amount'    => 0,
        ]);

        PaymentLog::create([
            'alumni_id' => $alumnus->id,
            'type'      => 'reset',
            'amount'    => 0,
            'note'      => "Payment reset to unpaid. Previous paid amount was ৳" . number_format($previousPaid),
            'actor'     => auth()->user()->name,
        ]);

        return back()->with('success', "{$alumnus->name}'s payment reset to unpaid.");
    }

    public function cancelPayment(Request $request, Alumni $alumnus)
    {
        $request->validate(['reason' => 'required|string|max:300']);

        $reg = $alumnus->eventRegistration;
        if (!$reg) return back()->with('error', 'No event registration found.');

        if ($reg->payment_status !== 'pending') {
            return back()->with('error', 'Payment is not in pending state — cannot cancel.');
        }

        $prevRef    = $reg->payment_reference;
        $prevMethod = $reg->payment_method;

        $reg->update([
            'payment_status'    => 'unpaid',
            'payment_reference' => null,
            'payment_method'    => null,
        ]);

        PaymentLog::create([
            'alumni_id' => $alumnus->id,
            'type'      => 'cancelled',
            'amount'    => 0,
            'method'    => $prevMethod,
            'reference' => $prevRef,
            'note'      => "Payment cancelled by admin. Method: " . strtoupper($prevMethod ?? 'manual') . ". Ref: {$prevRef}. Reason: {$request->reason}",
            'actor'     => auth()->user()->name,
        ]);

        return back()->with('success', "{$alumnus->name}'s pending payment has been cancelled.");
    }

    public function adjustPayment(Alumni $alumnus)
    {
        $reg = $alumnus->eventRegistration;
        if (!$reg) return back()->with('error', 'No event registration found.');

        $previousPaid = $reg->paid_amount ?? 0;
        $newTotal     = $reg->total_amount;

        $reg->update([
            'paid_amount'    => $newTotal,
            'payment_status' => 'paid',
        ]);

        $diff = $previousPaid - $newTotal;
        PaymentLog::create([
            'alumni_id' => $alumnus->id,
            'type'      => 'adjusted',
            'amount'    => $newTotal,
            'note'      => "Amount adjusted to ৳" . number_format($newTotal) . ". Credit of ৳" . number_format($diff) . " acknowledged (guests/driver removed).",
            'actor'     => auth()->user()->name,
        ]);

        return back()->with('success', "Payment adjusted to ৳" . number_format($newTotal) . " for {$alumnus->name}.");
    }

    public function storeNote(Request $request, Alumni $alumnus)
    {
        $request->validate(['note' => 'required|string|max:500']);

        AdminNote::create([
            'alumni_id' => $alumnus->id,
            'admin_id'  => auth()->id(),
            'note'      => $request->note,
        ]);

        return back()->with('success', 'Note added.');
    }

    public function destroyNote(AdminNote $note)
    {
        // Only the author can delete their own note
        if ($note->admin_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        return back()->with('success', 'Note deleted.');
    }
}

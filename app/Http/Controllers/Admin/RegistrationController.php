<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = EventRegistration::with('alumni')
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->tshirt_size, fn ($q, $s) => $q->where('tshirt_size', $s))
            ->when($request->search, fn ($q, $s) =>
                $q->whereHas('alumni', fn ($q) =>
                    $q->where('name', 'like', "%$s%")
                      ->orWhere('roll_number', 'like', "%$s%")
                )
            )
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $tshirtSummary = EventRegistration::selectRaw('tshirt_size, count(*) as total')
            ->groupBy('tshirt_size')
            ->orderBy('tshirt_size')
            ->pluck('total', 'tshirt_size');

        $totalGuests   = EventRegistration::sum('guest_count');
        $pendingCount  = EventRegistration::where('payment_status', 'pending')->count();
        $paidCount     = EventRegistration::where('payment_status', 'paid')->count();

        return view('admin.registrations.index', compact('registrations', 'tshirtSummary', 'totalGuests', 'pendingCount', 'paidCount'));
    }
}

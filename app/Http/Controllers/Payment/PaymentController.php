<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Models\PaymentLog;
use App\Models\PaymentTransaction;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(private SslCommerzService $sslcommerz) {}

    public function confirm()
    {
        $alumni = auth()->user();
        $registration = $alumni->eventRegistration;

        if (!$registration) {
            return redirect()->route('event.show')
                ->with('error', 'Please complete your event registration first.');
        }

        if ($registration->payment_status === 'paid') {
            return redirect()->route('dashboard')
                ->with('success', 'Your payment is already confirmed.');
        }

        $fee = config('sslcommerz.registration_fee');

        return view('payment.confirm', compact('alumni', 'registration', 'fee'));
    }

    public function manual(Request $request)
    {
        $request->validate([
            'payment_method'    => 'required|in:bkash,nagad,bank_transfer',
            'sender_number'     => 'nullable|string|max:20',
            'payment_reference' => 'required|string|max:100',
        ]);

        $alumni = auth()->user();
        $registration = $alumni->eventRegistration;

        if (!$registration) return redirect()->route('dashboard');

        // Block if already fully paid (no balance)
        if ($registration->paid_amount >= $registration->total_amount) {
            return redirect()->route('dashboard')->with('info', 'Your payment is already fully confirmed.');
        }

        $balanceDue = $registration->total_amount - $registration->paid_amount;

        $ref = strtoupper(trim($request->payment_reference));

        $registration->update([
            'payment_method'    => $request->payment_method,
            'payment_reference' => $ref,
            'payment_status'    => 'pending',
        ]);

        $label = $registration->paid_amount > 0 ? 'Additional payment submitted' : 'Payment submitted';
        PaymentLog::create([
            'alumni_id' => $alumni->id,
            'type'      => 'submitted',
            'amount'    => $balanceDue,
            'method'    => $request->payment_method,
            'reference' => $ref,
            'note'      => "{$label} — ৳" . number_format($balanceDue) . " via " . strtoupper($request->payment_method) . ". Ref: {$ref}. Awaiting admin confirmation.",
            'actor'     => $alumni->name,
        ]);

        $msg = $registration->paid_amount > 0
            ? "Additional payment of ৳" . number_format($balanceDue) . " submitted for verification."
            : 'Payment reference submitted! Pending admin verification.';

        return redirect()->route('event.show')->with('success', $msg);
    }

    public function initiate(Request $request)
    {
        $alumni = auth()->user();
        $registration = $alumni->eventRegistration;

        if (!$registration || $registration->payment_status === 'paid') {
            return redirect()->route('dashboard');
        }

        $fee      = config('sslcommerz.registration_fee');
        $tranId   = 'KZS-' . strtoupper(Str::random(10));

        // Create transaction record
        $transaction = PaymentTransaction::create([
            'alumni_id' => $alumni->id,
            'amount'    => $fee,
            'status'    => 'initiated',
            'gateway'   => 'sslcommerz',
            'gateway_transaction_id' => $tranId,
        ]);

        // Mark registration as payment pending
        $registration->update(['payment_status' => 'pending']);

        $gatewayUrl = $this->sslcommerz->initiate([
            'total_amount'  => $fee,
            'tran_id'       => $tranId,
            'success_url'   => route('payment.success'),
            'fail_url'      => route('payment.fail'),
            'cancel_url'    => route('payment.cancel'),
            'ipn_url'       => route('payment.ipn'),
            'cus_name'      => $alumni->name,
            'cus_email'     => $alumni->email,
            'cus_phone'     => $alumni->phone ?? '01700000000',
        ]);

        if (!$gatewayUrl) {
            $transaction->update(['status' => 'failed']);
            $registration->update(['payment_status' => 'unpaid']);

            return redirect()->route('payment.confirm')
                ->with('error', 'Could not connect to the payment gateway. Please try again.');
        }

        return redirect()->away($gatewayUrl);
    }

    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');
        $amount = $request->input('amount');

        $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)->first();

        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction not found.');
        }

        // Validate with SSLCommerz server to prevent URL tampering
        if (!$this->sslcommerz->validate($valId, $amount)) {
            $transaction->update([
                'status'           => 'failed',
                'gateway_response' => $request->all(),
            ]);
            return redirect()->route('payment.fail');
        }

        $transaction->update([
            'status'       => 'success',
            'gateway_ref'  => $valId,
            'gateway_response' => $request->all(),
        ]);

        $reg = EventRegistration::where('alumni_id', $transaction->alumni_id)->first();
        if ($reg) {
            $reg->update([
                'payment_status' => 'paid',
                'paid_amount'    => $reg->total_amount,
            ]);
        }

        PaymentLog::create([
            'alumni_id' => $transaction->alumni_id,
            'type'      => 'ssl_confirmed',
            'amount'    => $transaction->amount,
            'method'    => 'sslcommerz',
            'reference' => $tranId,
            'note'      => "Online payment confirmed via SSLCommerz — ৳" . number_format($transaction->amount) . ". Val ID: {$valId}",
            'actor'     => 'System',
        ]);

        return view('payment.success', ['alumni' => $transaction->alumni]);
    }

    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');

        if ($tranId) {
            $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)->first();

            if ($transaction) {
                $transaction->update([
                    'status'           => 'failed',
                    'gateway_response' => $request->all(),
                ]);

                EventRegistration::where('alumni_id', $transaction->alumni_id)
                    ->update(['payment_status' => 'unpaid']);
            }
        }

        return view('payment.failed', ['reason' => 'payment_failed']);
    }

    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');

        if ($tranId) {
            $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)->first();

            if ($transaction) {
                $transaction->update([
                    'status'           => 'cancelled',
                    'gateway_response' => $request->all(),
                ]);

                EventRegistration::where('alumni_id', $transaction->alumni_id)
                    ->update(['payment_status' => 'unpaid']);
            }
        }

        return view('payment.failed', ['reason' => 'cancelled']);
    }

    /**
     * IPN handler — SSLCommerz POSTs here server-to-server.
     * Must be publicly accessible (no auth middleware).
     */
    public function ipn(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');
        $amount = $request->input('amount');
        $status = $request->input('status');

        if ($status !== 'VALID' && $status !== 'VALIDATED') {
            return response('INVALID STATUS', 400);
        }

        $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)->first();

        if (!$transaction) {
            return response('NOT FOUND', 404);
        }

        if (!$this->sslcommerz->validate($valId, $amount)) {
            return response('VALIDATION FAILED', 400);
        }

        $transaction->update([
            'status'           => 'success',
            'gateway_ref'      => $valId,
            'gateway_response' => $request->all(),
        ]);

        $reg = EventRegistration::where('alumni_id', $transaction->alumni_id)->first();
        if ($reg) {
            $reg->update([
                'payment_status' => 'paid',
                'paid_amount'    => $reg->total_amount,
            ]);
        }

        return response('IPN_SUCCESS', 200);
    }
}

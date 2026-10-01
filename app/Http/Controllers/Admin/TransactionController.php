<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = PaymentTransaction::with('alumni')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) =>
                $q->whereHas('alumni', fn ($q) =>
                    $q->where('name', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%")
                )
            )
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $counts = [
            'all'       => PaymentTransaction::count(),
            'success'   => PaymentTransaction::where('status', 'success')->count(),
            'failed'    => PaymentTransaction::where('status', 'failed')->count(),
            'initiated' => PaymentTransaction::where('status', 'initiated')->count(),
            'cancelled' => PaymentTransaction::where('status', 'cancelled')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'counts'));
    }
}

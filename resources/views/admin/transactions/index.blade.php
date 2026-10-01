@extends('layouts.admin')
@section('title', 'Transactions')
@section('heading', 'Payment History')

@section('content')

{{-- Summary strip --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-1">Total</p>
        <p class="text-2xl font-bold text-gray-700">{{ $counts['all'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-green-200 p-4">
        <p class="text-xs text-green-500 mb-1">Success</p>
        <p class="text-2xl font-bold text-green-600">{{ $counts['success'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-red-200 p-4">
        <p class="text-xs text-red-400 mb-1">Failed</p>
        <p class="text-2xl font-bold text-red-600">{{ $counts['failed'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-yellow-200 p-4">
        <p class="text-xs text-yellow-500 mb-1">Initiated</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $counts['initiated'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-1">Cancelled</p>
        <p class="text-2xl font-bold text-gray-500">{{ $counts['cancelled'] }}</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.transactions.index') }}"
    class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All</option>
            <option value="success"   {{ request('status') === 'success'   ? 'selected' : '' }}>Success</option>
            <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
            <option value="initiated" {{ request('status') === 'initiated' ? 'selected' : '' }}>Initiated</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Search Alumni</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary w-48">
    </div>
    <button class="bg-primary text-white text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-opacity-90 transition">
        Filter
    </button>
    <a href="{{ route('admin.transactions.index') }}" class="text-sm text-gray-400 hover:text-primary">Reset</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Alumni</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">Tran ID</th>
                <th class="px-4 py-3 text-left">Amount</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Gateway Ref</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $txn)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    @if($txn->alumni)
                    <a href="{{ route('admin.alumni.show', $txn->alumni) }}"
                        class="font-medium text-gray-800 hover:text-primary hover:underline">
                        {{ $txn->alumni->name }}
                    </a>
                    <p class="text-xs text-gray-400">{{ $txn->alumni->email }}</p>
                    @else
                    <span class="text-gray-400 text-xs">Deleted</span>
                    @endif
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="font-mono text-xs text-gray-600">{{ $txn->gateway_transaction_id }}</span>
                </td>
                <td class="px-4 py-3 font-semibold text-gray-700">
                    ৳{{ number_format($txn->amount, 0) }}
                </td>
                <td class="px-4 py-3">
                    @php
                        $color = match($txn->status) {
                            'success'   => 'bg-green-100 text-green-700',
                            'failed'    => 'bg-red-100 text-red-700',
                            'initiated' => 'bg-yellow-100 text-yellow-700',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                            default     => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $color }}">
                        {{ ucfirst($txn->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="font-mono text-xs text-gray-500">{{ $txn->gateway_ref ?: '—' }}</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-400 hidden lg:table-cell">
                    {{ $txn->created_at->format('d M Y, H:i') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@endsection

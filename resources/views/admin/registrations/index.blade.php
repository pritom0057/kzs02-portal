@extends('layouts.admin')
@section('title', 'Registrations')
@section('heading', 'Event Registrations')

@section('content')

{{-- Summary row --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-1">Total Registered</p>
        <p class="text-2xl font-bold text-primary">{{ $registrations->total() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-blue-200 bg-blue-50 p-4">
        <p class="text-xs text-blue-400 mb-1">Pending Approval</p>
        <p class="text-2xl font-bold text-blue-600">{{ $pendingCount }}</p>
        <p class="text-xs text-blue-400">awaiting confirmation</p>
    </div>
    <div class="bg-white rounded-xl border border-green-200 p-4">
        <p class="text-xs text-gray-400 mb-1">Paid</p>
        <p class="text-2xl font-bold text-green-600">{{ $paidCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-2">T-Shirt Sizes</p>
        <div class="flex flex-wrap gap-1">
            @foreach($tshirtSummary as $size => $count)
            <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-1.5 py-0.5 rounded">
                {{ $size }}:{{ $count }}
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.registrations.index') }}"
    class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Payment Status</label>
        <select name="payment_status"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All</option>
            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">T-Shirt Size</label>
        <select name="tshirt_size"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All</option>
            @foreach(['S','M','L','XL','XXL','XXXL'] as $size)
            <option value="{{ $size }}" {{ request('tshirt_size') === $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or roll…"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary w-44">
    </div>
    <button class="bg-primary text-white text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-opacity-90 transition">
        Filter
    </button>
    <a href="{{ route('admin.registrations.index') }}" class="text-sm text-gray-400 hover:text-primary">Reset</a>
    <a href="{{ route('admin.export.registrations') }}"
        class="ml-auto bg-accent text-primary text-sm font-semibold px-4 py-1.5 rounded-lg hover:opacity-90 transition">
        ↓ Export CSV
    </a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">T-Shirt</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Guests</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Amount</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Method / Ref</th>
                <th class="px-4 py-3 text-left">Payment</th>
                <th class="px-4 py-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($registrations as $reg)
            <tr class="hover:bg-gray-50 transition {{ $reg->payment_status === 'pending' ? 'bg-blue-50/40' : '' }}">
                <td class="px-4 py-3">
                    <a href="{{ route('admin.alumni.show', $reg->alumni) }}"
                        class="font-medium text-gray-800 hover:text-primary hover:underline">
                        {{ $reg->alumni->name }}
                    </a>
                    <p class="text-xs text-gray-400">{{ $reg->alumni->roll_number ?: $reg->alumni->email }}</p>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                        {{ $reg->tshirt_size }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $reg->guest_count }}</td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <p class="text-gray-700 font-semibold">৳{{ number_format($reg->total_amount) }}</p>
                    @if($reg->paid_amount > 0 && $reg->paid_amount < $reg->total_amount)
                    <p class="text-xs text-red-600 font-semibold mt-0.5">Due: ৳{{ number_format($reg->total_amount - $reg->paid_amount) }}</p>
                    @elseif($reg->paid_amount >= $reg->total_amount && $reg->payment_status === 'paid')
                    <p class="text-xs text-green-600 mt-0.5">Fully paid</p>
                    @endif
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <p class="text-xs text-gray-600 capitalize">{{ $reg->payment_method ?: '—' }}</p>
                    @if($reg->payment_reference)
                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[140px]" title="{{ $reg->payment_reference }}">
                        {{ $reg->payment_reference }}
                    </p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ $reg->payment_status === 'paid'    ? 'bg-green-100 text-green-700' :
                           ($reg->payment_status === 'pending' ? 'bg-blue-100 text-blue-700'  : 'bg-orange-100 text-orange-700') }}">
                        {{ ucfirst($reg->payment_status ?? 'Unpaid') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @php
                        $balanceDue = max(0, $reg->total_amount - $reg->paid_amount);
                        $needsConfirm = $balanceDue > 0 || $reg->payment_status === 'pending';
                    @endphp
                    @if($needsConfirm)
                    <form method="POST" action="{{ route('admin.alumni.payment.confirm', $reg->alumni) }}">
                        @csrf
                        <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                            @if($reg->paid_amount > 0 && $balanceDue > 0)
                                ✓ +৳{{ number_format($balanceDue) }}
                            @else
                                ✓ Confirm
                            @endif
                        </button>
                    </form>
                    @elseif($reg->paid_amount > $reg->total_amount)
                    <form method="POST" action="{{ route('admin.alumni.payment.adjust', $reg->alumni) }}">
                        @csrf
                        <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                            ⇅ Adjust
                        </button>
                    </form>
                    @else
                    <span class="text-xs text-green-600 font-semibold">✓ Paid</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">No registrations found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($registrations->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm">
        {{ $registrations->links() }}
    </div>
    @endif
</div>

@endsection

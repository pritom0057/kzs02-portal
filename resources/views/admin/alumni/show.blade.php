@extends('layouts.admin')
@section('title', $alumnus->name)
@section('heading', $alumnus->name)

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.alumni.index') }}" class="text-sm text-gray-400 hover:text-primary transition">
        &larr; Back to Alumni
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Profile + actions --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Profile card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-200 flex-shrink-0 flex items-center justify-center text-xl font-bold text-gray-400">
                    @if($alumnus->photo_url)
                        <img src="{{ Storage::url($alumnus->photo_url) }}" class="w-full h-full object-cover" alt="">
                    @else
                        {{ strtoupper(substr($alumnus->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <p class="font-bold text-gray-800">{{ $alumnus->name }}</p>
                    <p class="text-xs text-gray-400">Roll: {{ $alumnus->roll_number }}</p>
                </div>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Email</dt>
                    <dd class="text-gray-700 break-all">{{ $alumnus->email }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Phone</dt>
                    <dd class="text-gray-700">{{ $alumnus->phone ?: '—' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Profession</dt>
                    <dd class="text-gray-700">{{ $alumnus->current_profession ?: '—' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Location</dt>
                    <dd class="text-gray-700">{{ $alumnus->current_location ?: '—' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Joined</dt>
                    <dd class="text-gray-700">{{ $alumnus->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Status</dt>
                    <dd>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $alumnus->status === 'verified' ? 'bg-green-100 text-green-700' :
                               ($alumnus->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($alumnus->status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Verify / Reject buttons --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-2">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Actions</p>
            @if($alumnus->status !== 'verified')
            <form method="POST" action="{{ route('admin.alumni.verify', $alumnus) }}">
                @csrf
                <button class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                    ✓ Verify Account
                </button>
            </form>
            @endif
            @if($alumnus->status !== 'rejected')
            <form method="POST" action="{{ route('admin.alumni.reject', $alumnus) }}">
                @csrf
                <button onclick="return confirm('Reject this alumni?')"
                    class="w-full bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold py-2 rounded-lg transition">
                    ✕ Reject Account
                </button>
            </form>
            @endif
        </div>

        {{-- Event registration --}}
        @if($alumnus->eventRegistration)
        @php
            $reg = $alumnus->eventRegistration;
            $balanceDue = max(0, $reg->total_amount - $reg->paid_amount);
            $overpaid   = $reg->paid_amount > $reg->total_amount;
            $fullyPaid  = $reg->paid_amount >= $reg->total_amount && $reg->payment_status === 'paid';
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">Event Registration</p>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">T-Shirt</dt>
                    <dd class="text-gray-700 font-semibold">{{ $reg->tshirt_size }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Guests</dt>
                    <dd class="text-gray-700">{{ $reg->guest_count }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Dietary</dt>
                    <dd class="text-gray-700">{{ $reg->dietary_notes ?: '—' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Total</dt>
                    <dd class="text-gray-700 font-semibold">৳{{ number_format($reg->total_amount) }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Paid</dt>
                    <dd class="font-semibold {{ $reg->paid_amount > 0 ? 'text-green-700' : 'text-gray-400' }}">
                        ৳{{ number_format($reg->paid_amount) }}
                    </dd>
                </div>
                @if($balanceDue > 0)
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Balance Due</dt>
                    <dd class="font-bold text-red-600">৳{{ number_format($balanceDue) }}</dd>
                </div>
                @endif
                @if($overpaid)
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Overpaid</dt>
                    <dd class="font-bold text-amber-600">৳{{ number_format($reg->paid_amount - $reg->total_amount) }} credit</dd>
                </div>
                @endif
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Method</dt>
                    <dd class="text-gray-700 capitalize">{{ $reg->payment_method ?: '—' }}</dd>
                </div>
                @if($reg->payment_reference)
                <div class="flex gap-3">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Reference</dt>
                    <dd class="text-gray-700 break-all">{{ $reg->payment_reference }}</dd>
                </div>
                @endif
                <div class="flex gap-3 items-center">
                    <dt class="w-24 text-gray-400 flex-shrink-0">Status</dt>
                    <dd>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full
                            {{ $reg->payment_status === 'paid' ? 'bg-green-100 text-green-700' :
                               ($reg->payment_status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($reg->payment_status ?? 'unpaid') }}
                        </span>
                        @if($reg->payment_status === 'paid' && $balanceDue > 0)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 ml-1">
                            Additional Required
                        </span>
                        @endif
                    </dd>
                </div>
            </dl>

            {{-- Payment action buttons --}}
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                @if($overpaid)
                <form method="POST" action="{{ route('admin.alumni.payment.adjust', $alumnus) }}">
                    @csrf
                    <button onclick="return confirm('Adjust to current total (credit the overpaid amount)?')"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2 rounded-lg transition">
                        ⇅ Adjust / Apply Credit (set paid = ৳{{ number_format($reg->total_amount) }})
                    </button>
                </form>
                @elseif($fullyPaid)
                <form method="POST" action="{{ route('admin.alumni.payment.reset', $alumnus) }}">
                    @csrf
                    <button onclick="return confirm('Reset payment to unpaid?')"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold py-2 rounded-lg transition">
                        ↺ Reset to Unpaid
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.alumni.payment.confirm', $alumnus) }}">
                    @csrf
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                        @if($reg->paid_amount > 0)
                            ✓ Confirm Additional Payment (৳{{ number_format($balanceDue) }})
                        @else
                            ✓ Confirm Payment (Mark as Paid)
                        @endif
                    </button>
                </form>
                @endif
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 text-sm text-gray-400 text-center">
            Not registered for event yet.
        </div>
        @endif
    </div>

    {{-- Right: Transactions + Admin notes --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Payment History --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">Payment History</p>
            @if($alumnus->paymentLogs->isEmpty())
                <p class="text-sm text-gray-400 text-center py-3">No payment activity yet.</p>
            @else
            <ul class="space-y-3">
                @foreach($alumnus->paymentLogs->sortByDesc('created_at') as $log)
                @php
                    [$icon, $color] = match($log->type) {
                        'confirmed'     => ['✓', 'bg-green-100 text-green-700'],
                        'ssl_confirmed' => ['✓', 'bg-green-100 text-green-700'],
                        'submitted'     => ['⏳', 'bg-blue-100 text-blue-700'],
                        'reset'         => ['↺', 'bg-orange-100 text-orange-700'],
                        'adjusted'      => ['⇅', 'bg-amber-100 text-amber-700'],
                        default         => ['·', 'bg-gray-100 text-gray-500'],
                    };
                @endphp
                <li class="flex items-start gap-3 text-sm">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full flex-shrink-0 mt-0.5 {{ $color }}">
                        {{ $icon }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-700">{{ $log->note }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $log->actor }} · {{ $log->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @if($log->amount > 0)
                    <span class="text-sm font-bold text-gray-700 flex-shrink-0">৳{{ number_format($log->amount) }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>

    {{-- Wrapping div continues for admin notes below --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-4">Admin Notes</p>

            {{-- Add note form --}}
            <form method="POST" action="{{ route('admin.alumni.notes.store', $alumnus) }}" class="mb-5">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="note" placeholder="Add a note about this alumnus…"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('note') border-red-400 @enderror"
                        maxlength="500" required>
                    <button class="bg-primary text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-opacity-90 transition flex-shrink-0">
                        Add
                    </button>
                </div>
                @error('note')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </form>

            {{-- Notes list --}}
            @if($alumnus->adminNotes->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">No notes yet.</p>
            @else
                <ul class="space-y-3">
                    @foreach($alumnus->adminNotes->sortByDesc('created_at') as $note)
                    <li class="bg-gray-50 rounded-lg px-4 py-3 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-700">{{ $note->note }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $note->admin->name }} · {{ $note->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        @if($note->admin_id === auth()->id())
                        <form method="POST" action="{{ route('admin.notes.destroy', $note) }}" class="flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this note?')"
                                class="text-xs text-red-400 hover:text-red-600 transition">
                                Delete
                            </button>
                        </form>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>
@endsection

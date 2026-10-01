@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Alumni',   'value' => $stats['total'],      'color' => 'bg-gray-100 text-gray-700'],
        ['label' => 'Pending',        'value' => $stats['pending'],     'color' => 'bg-yellow-100 text-yellow-700'],
        ['label' => 'Verified',       'value' => $stats['verified'],    'color' => 'bg-green-100 text-green-700'],
        ['label' => 'Rejected',       'value' => $stats['rejected'],    'color' => 'bg-red-100 text-red-700'],
        ['label' => 'Event Reg.',     'value' => $stats['registered'],  'color' => 'bg-blue-100 text-blue-700'],
        ['label' => 'Paid',           'value' => $stats['paid'],        'color' => 'bg-purple-100 text-purple-700'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-1">{{ $stat['label'] }}</p>
        <p class="text-2xl font-bold {{ $stat['color'] }} inline-block px-2 py-0.5 rounded">
            {{ $stat['value'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- Pending alumni queue --}}
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-700">Pending Approval</h2>
        <a href="{{ route('admin.alumni.index', ['status' => 'pending']) }}"
            class="text-xs text-primary hover:underline">View all &rarr;</a>
    </div>

    @if($pendingAlumni->isEmpty())
        <p class="px-5 py-6 text-sm text-gray-400">No pending alumni. All caught up!</p>
    @else
        <ul class="divide-y divide-gray-100">
            @foreach($pendingAlumni as $a)
            <li class="px-5 py-3 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-medium text-sm text-gray-800 truncate">{{ $a->name }}</p>
                    <p class="text-xs text-gray-400">Roll: {{ $a->roll_number }} · {{ $a->email }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.alumni.verify', $a) }}">
                        @csrf
                        <button class="text-xs bg-green-100 text-green-700 font-semibold px-3 py-1 rounded hover:bg-green-200 transition">
                            Verify
                        </button>
                    </form>
                    <a href="{{ route('admin.alumni.show', $a) }}"
                        class="text-xs bg-gray-100 text-gray-600 font-semibold px-3 py-1 rounded hover:bg-gray-200 transition">
                        View
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    @endif
</div>

@endsection

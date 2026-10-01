@extends('layouts.admin')
@section('title', 'Alumni')
@section('heading', 'Alumni Management')

@section('content')

{{-- Tabs + search --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
    <div class="flex gap-1 text-sm">
        @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
        <a href="{{ route('admin.alumni.index', ['status' => $key, 'search' => request('search')]) }}"
            class="px-3 py-1.5 rounded-lg font-medium transition
            {{ $status === $key
                ? 'bg-primary text-white'
                : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            {{ $label }}
            <span class="ml-1 text-xs opacity-70">({{ $counts[$key] }})</span>
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.alumni.index') }}" class="flex gap-2 sm:ml-auto">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search name, roll, email…"
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary w-52">
        <button class="bg-primary text-white text-sm px-3 py-1.5 rounded-lg hover:bg-opacity-90 transition">
            Search
        </button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Roll</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Email</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Phone</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">Joined</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($alumni as $a)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-800">
                    <a href="{{ route('admin.alumni.show', $a) }}" class="hover:text-primary hover:underline">
                        {{ $a->name }}
                    </a>
                    @if($a->admin_notes_count > 0)
                        <span class="ml-1 text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded">
                            {{ $a->admin_notes_count }} note{{ $a->admin_notes_count > 1 ? 's' : '' }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $a->roll_number }}</td>
                <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $a->email }}</td>
                <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ $a->phone ?: '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                        {{ $a->status === 'verified' ? 'bg-green-100 text-green-700' :
                           ($a->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 hidden sm:table-cell text-xs">
                    {{ $a->created_at->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-1">
                        @if($a->status !== 'verified')
                        <form method="POST" action="{{ route('admin.alumni.verify', $a) }}">
                            @csrf
                            <button class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-1 rounded hover:bg-green-200 transition">
                                Verify
                            </button>
                        </form>
                        @endif
                        @if($a->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.alumni.reject', $a) }}">
                            @csrf
                            <button class="text-xs bg-red-100 text-red-700 font-semibold px-2 py-1 rounded hover:bg-red-200 transition"
                                onclick="return confirm('Reject {{ $a->name }}?')">
                                Reject
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('admin.alumni.show', $a) }}"
                            class="text-xs bg-gray-100 text-gray-600 font-semibold px-2 py-1 rounded hover:bg-gray-200 transition">
                            View
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                    No alumni found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($alumni->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm">
        {{ $alumni->links() }}
    </div>
    @endif
</div>

@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Alumni;

class DirectoryController extends Controller
{
    public function index()
    {
        $alumni = Alumni::where('status', 'verified')
            ->with('eventRegistration')
            ->orderByRaw('EXISTS(SELECT 1 FROM event_registrations WHERE event_registrations.alumni_id = alumni.id) DESC')
            ->orderBy('name')
            ->get();

        $totalCount   = $alumni->count();
        $comingCount  = $alumni->filter(fn($a) => $a->eventRegistration)->count();
        $paidCount    = $alumni->filter(fn($a) => $a->eventRegistration?->payment_status === 'paid')->count();

        return view('directory.index', compact('alumni', 'totalCount', 'comingCount', 'paidCount'));
    }
}

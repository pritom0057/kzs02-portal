<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\EventRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'      => Alumni::where('role', 'alumni')->count(),
            'pending'    => Alumni::where('status', 'pending')->where('role', 'alumni')->count(),
            'verified'   => Alumni::where('status', 'verified')->where('role', 'alumni')->count(),
            'rejected'   => Alumni::where('status', 'rejected')->where('role', 'alumni')->count(),
            'registered' => EventRegistration::count(),
            'paid'       => EventRegistration::where('payment_status', 'paid')->count(),
        ];

        $pendingAlumni = Alumni::where('status', 'pending')
            ->where('role', 'alumni')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingAlumni'));
    }
}

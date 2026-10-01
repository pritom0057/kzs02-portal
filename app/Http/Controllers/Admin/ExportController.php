<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\EventRegistration;

class ExportController extends Controller
{
    public function alumni()
    {
        $rows = Alumni::where('role', 'alumni')
            ->orderBy('name')
            ->get(['name', 'roll_number', 'email', 'phone', 'status',
                   'current_profession', 'current_location', 'created_at']);

        return $this->csvResponse('kzs2002_alumni_' . now()->format('Ymd') . '.csv', function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Name', 'Roll Number', 'Email', 'Phone', 'Status',
                           'Profession', 'Location', 'Registered At']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name,
                    $r->roll_number,
                    $r->email,
                    $r->phone,
                    $r->status,
                    $r->current_profession,
                    $r->current_location,
                    $r->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($out);
        });
    }

    public function registrations()
    {
        $rows = EventRegistration::with('alumni')
            ->orderBy('created_at')
            ->get();

        return $this->csvResponse('kzs2002_registrations_' . now()->format('Ymd') . '.csv', function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Name', 'Roll Number', 'Email', 'Phone',
                           'T-Shirt Size', 'Guests', 'Dietary Notes', 'Payment Status', 'Registered At']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->alumni->name,
                    $r->alumni->roll_number,
                    $r->alumni->email,
                    $r->alumni->phone,
                    $r->tshirt_size,
                    $r->guest_count,
                    $r->dietary_notes,
                    $r->payment_status,
                    $r->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($out);
        });
    }

    private function csvResponse(string $filename, callable $writer): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->stream($writer, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

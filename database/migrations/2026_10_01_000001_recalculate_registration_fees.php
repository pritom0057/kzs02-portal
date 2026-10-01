<?php

use App\Models\EventRegistration;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Recalculate totals for all unpaid/pending registrations using the new fee structure:
        // Base 2002 (member + spouse + children) + guests*1000 + driver*500 + donation
        EventRegistration::whereIn('payment_status', ['unpaid', 'pending'])->each(function ($reg) {
            $guestsCount    = is_array($reg->guests_details)
                ? count(array_filter($reg->guests_details, fn($g) => !empty($g['name'])))
                : 0;
            $driverIncluded = (bool) $reg->driver_included;
            $donationAmount = (int) ($reg->donation_amount ?? 0);

            $reg->total_amount = EventRegistration::calculateTotal(
                false, 0, $guestsCount, $driverIncluded, $donationAmount
            );
            $reg->save();
        });
    }

    public function down(): void
    {
        // Restore old fee structure: 2000 + spouse*1000 + children*500 + guests*1000 + driver*500 + donation
        EventRegistration::whereIn('payment_status', ['unpaid', 'pending'])->each(function ($reg) {
            $bringSpouse    = (bool) $reg->bring_spouse;
            $childrenCount  = is_array($reg->children_details)
                ? count(array_filter($reg->children_details, fn($c) => !empty($c['name'])))
                : 0;
            $guestsCount    = is_array($reg->guests_details)
                ? count(array_filter($reg->guests_details, fn($g) => !empty($g['name'])))
                : 0;
            $driverIncluded = (bool) $reg->driver_included;
            $donationAmount = (int) ($reg->donation_amount ?? 0);

            $reg->total_amount = 2000
                + ($bringSpouse ? 1000 : 0)
                + ($childrenCount * 500)
                + ($guestsCount * 1000)
                + ($driverIncluded ? 500 : 0)
                + $donationAmount;
            $reg->save();
        });
    }
};

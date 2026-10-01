<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'alumni_id', 'guest_count', 'bring_spouse', 'spouse_name', 'spouse_name_bn',
        'children_count', 'children_details', 'guests_count', 'guests_details',
        'driver_included', 'donation_amount', 'dietary_notes', 'tshirt_size',
        'meal_preference', 'special_requirements',
        'total_amount', 'paid_amount', 'payment_status',
        'payment_method', 'payment_reference',
    ];

    protected $casts = [
        'bring_spouse'    => 'boolean',
        'driver_included' => 'boolean',
        'children_details'=> 'array',
        'guests_details'  => 'array',
    ];

    public static function calculateTotal(bool $spouse, int $children, int $guests = 0, bool $driver = false, int $donation = 0): int
    {
        // Base 2002 covers member + spouse + all children
        return 2002
            + ($guests * 1000)
            + ($driver ? 500 : 0)
            + $donation;
    }

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}

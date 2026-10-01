<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'alumni_id',
        'amount',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_ref',
        'gateway_response',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}

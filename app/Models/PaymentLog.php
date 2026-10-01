<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'alumni_id', 'type', 'amount', 'method', 'reference', 'note', 'actor',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}

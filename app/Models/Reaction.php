<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = ['reactionable_id', 'reactionable_type', 'alumni_id', 'type'];

    public function reactionable()
    {
        return $this->morphTo();
    }

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}

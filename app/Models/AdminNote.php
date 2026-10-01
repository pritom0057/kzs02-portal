<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNote extends Model
{
    protected $fillable = ['alumni_id', 'admin_id', 'note'];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function admin()
    {
        return $this->belongsTo(Alumni::class, 'admin_id');
    }
}

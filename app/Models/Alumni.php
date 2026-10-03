<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Alumni extends Authenticatable
{
    use Notifiable;

    protected $table = 'alumni';

    protected $fillable = [
        'name', 'name_bn', 'roll_number', 'email', 'phone', 'mobile', 'whatsapp',
        'emergency_contact', 'password', 'photo_url', 'family_photo_url',
        'father_name', 'mother_name',
        'present_address', 'permanent_address',
        'current_profession', 'current_location',
        'school_class', 'school_shift', 'section', 'higher_education',
        'organization', 'designation',
        'spouse_name', 'spouse_contact', 'children',
        'facebook_url', 'linkedin_url',
        'show_in_directory',
        'status', 'role', 'theme', 'lang', 'email_verified',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified'   => 'boolean',
        'show_in_directory'=> 'boolean',
        'children'         => 'array',
    ];

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isVerified(): bool { return $this->status === 'verified'; }

    public function eventRegistration()    { return $this->hasOne(EventRegistration::class); }
    public function adminNotes()           { return $this->hasMany(AdminNote::class); }
    public function paymentTransactions()  { return $this->hasMany(PaymentTransaction::class); }
    public function paymentLogs()          { return $this->hasMany(PaymentLog::class); }
}

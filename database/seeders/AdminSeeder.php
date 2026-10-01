<?php

namespace Database\Seeders;

use App\Models\Alumni;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Alumni::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@kzs2002.com')],
            [
                'name'           => env('ADMIN_NAME', 'Admin'),
                'roll_number'    => 'ADMIN001',
                'password'       => Hash::make(env('ADMIN_PASSWORD', 'changeme123')),
                'role'           => 'admin',
                'status'         => 'verified',
                'email_verified' => true,
            ]
        );

        $this->command->info('Admin account created. Login with roll number: ADMIN001');
    }
}

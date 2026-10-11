<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `event_registrations` MODIFY COLUMN `payment_method` ENUM('bkash','nagad','rocket','bank_transfer') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `event_registrations` MODIFY COLUMN `payment_method` ENUM('bkash','nagad','bank_transfer') NULL");
    }
};

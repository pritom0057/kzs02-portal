<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_id')->constrained('alumni')->onDelete('cascade');
            $table->unsignedTinyInteger('guest_count')->default(0);
            $table->string('dietary_notes')->nullable();
            $table->enum('tshirt_size', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable();
            $table->enum('payment_status', ['unpaid', 'pending', 'paid'])->default('unpaid');
            $table->timestamps();

            $table->unique('alumni_id'); // one registration per alumnus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};

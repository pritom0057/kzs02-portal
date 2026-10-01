<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_id')->constrained('alumni')->cascadeOnDelete();
            $table->enum('type', ['submitted', 'confirmed', 'reset', 'adjusted', 'ssl_confirmed', 'ssl_failed']);
            $table->unsignedInteger('amount')->default(0);
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->string('note');
            $table->string('actor')->default('System');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};

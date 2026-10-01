<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactionable');
            $table->unsignedBigInteger('alumni_id');
            $table->foreign('alumni_id')->references('id')->on('alumni')->cascadeOnDelete();
            $table->enum('type', ['like', 'dislike']);
            $table->unique(['reactionable_id', 'reactionable_type', 'alumni_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};

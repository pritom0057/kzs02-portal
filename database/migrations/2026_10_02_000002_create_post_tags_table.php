<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->unsignedBigInteger('alumni_id');
            $table->foreign('alumni_id')->references('id')->on('alumni')->cascadeOnDelete();
            $table->unique(['post_id', 'alumni_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tags');
    }
};

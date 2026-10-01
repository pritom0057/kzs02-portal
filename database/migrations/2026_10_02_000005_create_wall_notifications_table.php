<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wall_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_id');
            $table->unsignedBigInteger('actor_id');
            $table->enum('type', ['tagged', 'commented', 'replied', 'liked', 'disliked']);
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('recipient_id')->references('id')->on('alumni')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('alumni')->cascadeOnDelete();
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreign('comment_id')->references('id')->on('comments')->nullOnDelete();

            $table->index('recipient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wall_notifications');
    }
};

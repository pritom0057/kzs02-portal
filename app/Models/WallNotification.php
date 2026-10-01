<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WallNotification extends Model
{
    protected $table = 'wall_notifications';

    protected $fillable = ['recipient_id', 'actor_id', 'type', 'post_id', 'comment_id', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function recipient() { return $this->belongsTo(Alumni::class, 'recipient_id'); }
    public function actor()     { return $this->belongsTo(Alumni::class, 'actor_id'); }
    public function post()      { return $this->belongsTo(Post::class); }
    public function comment()   { return $this->belongsTo(Comment::class); }

    public function isRead(): bool { return $this->read_at !== null; }

    public function getMessage(): string
    {
        $actor = $this->actor->name;
        return match($this->type) {
            'tagged'    => "{$actor} tagged you in a post",
            'commented' => "{$actor} commented on your post",
            'replied'   => "{$actor} replied to your comment",
            'liked'     => "{$actor} liked your post",
            'disliked'  => "{$actor} disliked your post",
            default     => "{$actor} interacted with your post",
        };
    }
}

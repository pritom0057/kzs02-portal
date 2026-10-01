<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['alumni_id', 'content', 'photo_url'];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Alumni::class, 'post_tags');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->with(['alumni', 'reactions', 'replies.alumni', 'replies.reactions'])
            ->latest();
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->reactions->where('type', 'dislike')->count();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\WallNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content'   => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'post_id'   => $post->id,
            'alumni_id' => auth()->id(),
            'parent_id' => $request->parent_id ?: null,
            'content'   => $request->content,
        ]);

        $actorId = auth()->id();
        if ($comment->parent_id) {
            $parent = Comment::find($comment->parent_id);
            if ($parent && $parent->alumni_id !== $actorId) {
                WallNotification::create([
                    'recipient_id' => $parent->alumni_id,
                    'actor_id'     => $actorId,
                    'type'         => 'replied',
                    'post_id'      => $post->id,
                    'comment_id'   => $comment->id,
                ]);
            }
        } elseif ($post->alumni_id !== $actorId) {
            WallNotification::create([
                'recipient_id' => $post->alumni_id,
                'actor_id'     => $actorId,
                'type'         => 'commented',
                'post_id'      => $post->id,
                'comment_id'   => $comment->id,
            ]);
        }

        $comment->load('alumni');
        $alumni = $comment->alumni;
        $photo  = $alumni->photo_url
            ? (str_starts_with($alumni->photo_url, 'uploads/') ? asset($alumni->photo_url) : asset('storage/' . $alumni->photo_url))
            : null;

        return response()->json([
            'success' => true,
            'comment' => [
                'id'         => $comment->id,
                'content'    => e($comment->content),
                'parent_id'  => $comment->parent_id,
                'author'     => $alumni->name,
                'photo'      => $photo,
                'initial'    => strtoupper(substr($alumni->name, 0, 1)),
                'time'       => 'Just now',
                'can_delete' => true,
                'delete_url' => route('comments.destroy', $comment),
            ],
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->alumni_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['content' => 'required|string|max:500']);

        $comment->update(['content' => $request->content]);

        return response()->json([
            'success'   => true,
            'content'   => e($comment->content),
            'edited'    => $comment->updated_at->gt($comment->created_at),
            'edited_at' => $comment->updated_at->diffForHumans(),
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->alumni_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\WallNotification;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function togglePost(Request $request, Post $post)
    {
        return $this->toggle($request, $post);
    }

    public function toggleComment(Request $request, Comment $comment)
    {
        return $this->toggle($request, $comment);
    }

    public function listPost(Post $post)
    {
        return $this->listReactors($post);
    }

    public function listComment(Comment $comment)
    {
        return $this->listReactors($comment);
    }

    private function listReactors($model)
    {
        $reactions = $model->reactions()->with('alumni')->get();

        $map = fn($r) => [
            'name'    => $r->alumni->name,
            'photo'   => $r->alumni->photo_url
                ? (str_starts_with($r->alumni->photo_url, 'uploads/')
                    ? asset($r->alumni->photo_url)
                    : asset('storage/' . $r->alumni->photo_url))
                : null,
            'initial' => strtoupper(substr($r->alumni->name, 0, 1)),
        ];

        return response()->json([
            'likes'    => $reactions->where('type', 'like')->map($map)->values(),
            'dislikes' => $reactions->where('type', 'dislike')->map($map)->values(),
        ]);
    }

    private function toggle(Request $request, $model)
    {
        $request->validate(['type' => 'required|in:like,dislike']);

        $actorId  = auth()->id();
        $existing = $model->reactions()->where('alumni_id', $actorId)->first();
        $notifyType = null;

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete();
            } else {
                $existing->update(['type' => $request->type]);
                $notifyType = $request->type;
            }
        } else {
            $model->reactions()->create([
                'alumni_id' => $actorId,
                'type'      => $request->type,
            ]);
            $notifyType = $request->type;
        }

        if ($notifyType && $model->alumni_id !== $actorId) {
            $postId    = $model instanceof Post ? $model->id : $model->post_id;
            $commentId = $model instanceof Comment ? $model->id : null;
            WallNotification::create([
                'recipient_id' => $model->alumni_id,
                'actor_id'     => $actorId,
                'type'         => $notifyType === 'like' ? 'liked' : 'disliked',
                'post_id'      => $postId,
                'comment_id'   => $commentId,
            ]);
        }

        $model->load('reactions');

        return response()->json([
            'likes'         => $model->reactions->where('type', 'like')->count(),
            'dislikes'      => $model->reactions->where('type', 'dislike')->count(),
            'user_reaction' => $model->reactions->where('alumni_id', $actorId)->first()?->type,
        ]);
    }
}

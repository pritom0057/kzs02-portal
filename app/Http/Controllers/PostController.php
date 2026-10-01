<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function update(Request $request, Post $post)
    {
        if ($post->alumni_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['content' => 'required|string|max:1000']);

        $post->update(['content' => $request->content]);

        return response()->json([
            'success' => true,
            'content' => e($post->content),
            'edited'  => $post->updated_at->gt($post->created_at),
            'edited_at' => $post->updated_at->diffForHumans(),
        ]);
    }

    public function destroy(Post $post)
    {
        if ($post->alumni_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($post->photo_url) {
            @unlink(public_path($post->photo_url));
        }

        $post->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Post deleted.');
    }
}

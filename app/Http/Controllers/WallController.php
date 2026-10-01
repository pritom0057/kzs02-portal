<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Comment;
use App\Models\Post;
use App\Models\WallNotification;
use Illuminate\Http\Request;

class WallController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = Post::with([
            'alumni',
            'tags',
            'reactions',
            'comments' => fn($q) => $q->whereNull('parent_id')
                ->with(['alumni', 'reactions', 'replies.alumni', 'replies.reactions'])
                ->latest(),
        ])->latest();

        if ($filter === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($filter === 'month') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('wall.index', compact('posts', 'filter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content'    => 'nullable|string|max:1000',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tagged_ids' => 'nullable|string',
        ]);

        if (!$request->filled('content') && !$request->hasFile('photo')) {
            return back()->with('error', 'Please write something or upload a photo.');
        }

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $dir = public_path('uploads/wall');
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file     = $request->file('photo');
            $filename = 'wall_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $photoUrl = 'uploads/wall/' . $filename;
        }

        $post = Post::create([
            'alumni_id' => auth()->id(),
            'content'   => $request->content,
            'photo_url' => $photoUrl,
        ]);

        if ($request->filled('tagged_ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->tagged_ids)));
            if ($ids) {
                $post->tags()->sync($ids);
                $actorId = auth()->id();
                foreach ($ids as $taggedId) {
                    if ($taggedId !== $actorId) {
                        WallNotification::create([
                            'recipient_id' => $taggedId,
                            'actor_id'     => $actorId,
                            'type'         => 'tagged',
                            'post_id'      => $post->id,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Post shared!');
    }

    public function poll(Request $request)
    {
        $since        = (int) $request->input('since', 0);
        $latestPostId = (int) $request->input('latest_post_id', 0);
        $postIds      = array_filter(array_map('intval', explode(',', $request->input('post_ids', ''))));

        $sinceTime = $since ? \Carbon\Carbon::createFromTimestamp($since) : now()->subSeconds(6);

        // Count posts newer than what the client has
        $newPostCount   = $latestPostId ? Post::where('id', '>', $latestPostId)->count() : 0;
        $newestPostId   = Post::max('id') ?? $latestPostId;

        // New comments for open posts since last check
        $newComments = [];
        if (!empty($postIds)) {
            $me = auth()->user();
            Comment::with('alumni')
                ->whereIn('post_id', $postIds)
                ->where('created_at', '>', $sinceTime)
                ->get()
                ->each(function ($c) use (&$newComments, $me) {
                    $photo = $c->alumni->photo_url
                        ? (str_starts_with($c->alumni->photo_url, 'uploads/')
                            ? asset($c->alumni->photo_url)
                            : asset('storage/' . $c->alumni->photo_url))
                        : null;

                    $newComments[(string) $c->post_id][] = [
                        'id'         => $c->id,
                        'content'    => e($c->content),
                        'parent_id'  => $c->parent_id,
                        'author'     => $c->alumni->name,
                        'photo'      => $photo,
                        'initial'    => strtoupper(substr($c->alumni->name, 0, 1)),
                        'time'       => $c->created_at->diffForHumans(),
                        'can_delete' => $c->alumni_id === $me->id || $me->isAdmin(),
                        'delete_url' => route('comments.destroy', $c),
                    ];
                });
        }

        $unreadNotifCount = WallNotification::where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'new_post_count'            => $newPostCount,
            'latest_post_id'            => $newestPostId,
            'new_comments'              => $newComments,
            'server_time'               => now()->timestamp,
            'unread_notification_count' => $unreadNotifCount,
        ]);
    }

    public function goToPost(Post $post)
    {
        $position = Post::where('id', '>', $post->id)->count() + 1;
        $page     = (int) ceil($position / 10);
        $url      = route('wall.index') . ($page > 1 ? '?page=' . $page : '') . '#post-' . $post->id;
        return redirect($url);
    }

    public function searchAlumni(Request $request)
    {
        $q = trim($request->input('q', ''));
        $results = Alumni::where('status', 'verified')
            ->where('id', '!=', auth()->id())
            ->where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'photo_url')
            ->limit($q === '' ? 200 : 10)
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'name'  => $a->name,
                'photo' => $a->photo_url
                    ? (str_starts_with($a->photo_url, 'uploads/') ? asset($a->photo_url) : asset('storage/' . $a->photo_url))
                    : null,
            ]);

        return response()->json($results);
    }
}

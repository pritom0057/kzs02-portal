<?php

namespace App\Http\Controllers;

use App\Models\WallNotification;
use Illuminate\Http\Request;

class WallNotificationController extends Controller
{
    public function index()
    {
        $notifications = WallNotification::with('actor')
            ->where('recipient_id', auth()->id())
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                $photo = $n->actor->photo_url
                    ? (str_starts_with($n->actor->photo_url, 'uploads/')
                        ? asset($n->actor->photo_url)
                        : asset('storage/' . $n->actor->photo_url))
                    : null;

                return [
                    'id'      => $n->id,
                    'message' => $n->getMessage(),
                    'post_id' => $n->post_id,
                    'url'     => route('wall.go-to-post', $n->post_id),
                    'read'    => $n->isRead(),
                    'time'    => $n->created_at->diffForHumans(),
                    'photo'   => $photo,
                    'initial' => strtoupper(substr($n->actor->name, 0, 1)),
                ];
            });

        WallNotification::where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($notifications);
    }

    public function markRead()
    {
        WallNotification::where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = WallNotification::where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}

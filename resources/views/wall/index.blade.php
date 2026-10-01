@extends('layouts.app')
@section('title', 'Alumni Wall — KZS 2002 Reunion')
@section('content')

@php
    $me = auth()->user();
    $myPhoto = $me->photo_url
        ? (str_starts_with($me->photo_url, 'uploads/') ? asset($me->photo_url) : asset('storage/' . $me->photo_url))
        : null;
@endphp

<div class="max-w-2xl mx-auto">

    {{-- Page heading --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-bold text-primary"><i class="fa-solid fa-users-line mr-2"></i>Alumni Wall</h1>
        <p class="text-xs text-gray-400 dark:text-gray-500">Share memories with your batch</p>
    </div>

    {{-- New posts banner (shown by polling JS) --}}
    <div id="newPostsBanner" class="hidden mb-4 bg-primary/10 dark:bg-primary/20 border border-primary/30 rounded-xl px-4 py-2.5 flex items-center justify-between gap-3">
        <span class="text-sm text-primary font-medium">
            <i class="fa fa-arrow-up mr-1.5"></i>
            <span id="newPostsCount">0</span> new post(s) — click to refresh
        </span>
        <button onclick="loadNewPosts()" class="bg-primary text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-red-700 transition">
            Refresh
        </button>
    </div>

    {{-- ================================================================
         COMPOSER CARD
    ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('wall.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
            @csrf
            <div class="flex gap-3">
                {{-- My avatar --}}
                <div class="flex-shrink-0">
                    @if($myPhoto)
                        <img src="{{ $myPhoto }}" alt="{{ $me->name }}"
                             class="w-10 h-10 rounded-full object-cover ring-2 ring-primary/30">
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($me->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Composer body --}}
                <div class="flex-1 min-w-0">
                    <div class="relative">
                        <textarea id="postContent" name="content" rows="3"
                            placeholder="What's on your mind, {{ $me->name }}? Type @ to mention someone…"
                            maxlength="1000"
                            oninput="updatePostBtn(); handleMentionInput(this)"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none transition"></textarea>
                        <div id="mentionDropdown"
                             class="hidden absolute z-30 left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-xl overflow-hidden max-h-48 overflow-y-auto"></div>
                    </div>

                    {{-- Character counter --}}
                    <div class="flex justify-end mt-0.5 mb-2">
                        <span id="charCounter" class="text-xs text-gray-400 dark:text-gray-500">0 / 1000</span>
                    </div>

                    {{-- Photo preview --}}
                    <div id="photoPreviewWrap" class="hidden mb-2 relative inline-block">
                        <img id="photoPreview" src="" alt="Preview"
                             class="h-32 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                        <button type="button" onclick="clearWallPhoto()"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-primary text-white rounded-full text-xs flex items-center justify-center hover:bg-red-700 transition">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    {{-- Tag chips --}}
                    <div id="tagChips" class="flex flex-wrap gap-1.5 mb-2"></div>

                    {{-- Hidden tagged_ids --}}
                    <input type="hidden" name="tagged_ids" id="taggedIds" value="">

                    {{-- Tag people input --}}
                    <div class="relative mb-3" id="tagInputWrap">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-1.5">
                            <i class="fa fa-tag text-gray-400 text-xs"></i>
                            <input type="text" id="tagInput" placeholder="Tag a classmate…"
                                autocomplete="off"
                                oninput="searchTags(this.value)"
                                onfocus="searchTags(this.value)"
                                class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none">
                        </div>
                        <div id="tagDropdown"
                             class="hidden absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg overflow-hidden">
                        </div>
                    </div>

                    {{-- Action bar --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            {{-- Photo upload button --}}
                            <button type="button" onclick="document.getElementById('wallPhotoInput').click()"
                                class="flex items-center gap-1.5 text-sm text-kgray dark:text-gray-400 hover:text-kgreen transition px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fa fa-image text-kgreen"></i>
                                <span>Photo</span>
                            </button>
                            <input type="file" id="wallPhotoInput" name="photo" accept="image/*"
                                   class="hidden" onchange="previewWallPhoto(this)">
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="postSubmitBtn"
                            class="bg-primary text-white text-sm font-semibold px-5 py-1.5 rounded-xl hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fa fa-paper-plane mr-1"></i> Post
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Filter bar --}}
    <div class="flex items-center gap-2 mb-4 flex-wrap">
        <span class="text-xs text-gray-400 dark:text-gray-500 mr-1">Show:</span>
        @foreach(['all' => 'All Time', 'month' => 'This Month', 'week' => 'This Week'] as $val => $label)
        <a href="{{ route('wall.index', ['filter' => $val]) }}"
           class="text-xs px-3 py-1.5 rounded-full font-medium border transition
               {{ $filter === $val
                   ? 'bg-primary text-white border-primary'
                   : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- ================================================================
         FEED
    ================================================================ --}}
    <div id="post-feed">
    @forelse($posts as $post)
    @php
        $authorPhoto = $post->alumni->photo_url
            ? (str_starts_with($post->alumni->photo_url, 'uploads/')
                ? asset($post->alumni->photo_url)
                : asset('storage/' . $post->alumni->photo_url))
            : null;
        $userReaction = $post->reactions->where('alumni_id', $me->id)->first()?->type;
        $likes        = $post->reactions->where('type', 'like')->count();
        $dislikes     = $post->reactions->where('type', 'dislike')->count();
        $commentCount = $post->allComments()->count();
        $tagNames     = $post->tags->pluck('name');
    @endphp

    <div id="post-{{ $post->id }}"
         class="post-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-5 overflow-hidden transition-all duration-300">

        {{-- Post header --}}
        <div class="flex items-start gap-3 p-4 pb-2">
            {{-- Author avatar --}}
            <div class="flex-shrink-0">
                @if($authorPhoto)
                    <img src="{{ $authorPhoto }}" alt="{{ $post->alumni->name }}"
                         class="w-10 h-10 rounded-full object-cover ring-2 ring-primary/20">
                @else
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($post->alumni->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Author info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $post->alumni->name }}</span>
                        @if($tagNames->isNotEmpty())
                            <span class="text-gray-400 dark:text-gray-500 text-xs">
                                — with {{ $tagNames->implode(', ') }}
                            </span>
                        @endif
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 flex items-center gap-1.5">
                            {{ $post->created_at->diffForHumans() }}
                            @if($post->updated_at->gt($post->created_at))
                            <span id="post-edited-label-{{ $post->id }}" class="text-gray-300 dark:text-gray-600 italic">(Edited)</span>
                            @else
                            <span id="post-edited-label-{{ $post->id }}" class="hidden text-gray-300 dark:text-gray-600 italic">(Edited)</span>
                            @endif
                        </div>
                    </div>

                    {{-- Edit / Delete (own post or admin) --}}
                    @if($post->alumni_id === $me->id || $me->isAdmin())
                    <div class="flex items-center gap-1">
                        @if($post->alumni_id === $me->id && $post->content)
                        <button onclick="toggleEditPost({{ $post->id }})"
                            class="text-gray-300 dark:text-gray-600 hover:text-blue-500 transition text-xs p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            title="Edit post">
                            <i class="fa fa-pen-to-square"></i>
                        </button>
                        @endif
                        <button onclick="deletePost({{ $post->id }})"
                            class="text-gray-300 dark:text-gray-600 hover:text-primary transition text-xs p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            title="Delete post">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Post content --}}
        @if($post->content)
        <div id="post-content-{{ $post->id }}" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 leading-relaxed">
            @if(strlen($post->content) > 200)
                <span class="post-text-short">{{ substr($post->content, 0, 200) }}<span class="text-gray-400">…</span>
                    <button onclick="expandPost(this)" class="text-primary text-xs font-semibold ml-1 hover:underline">See more</button>
                </span>
                <span class="post-text-full hidden" data-full="{{ $post->content }}">{{ $post->content }}
                    <button onclick="collapsePost(this)" class="text-primary text-xs font-semibold ml-1 hover:underline">See less</button>
                </span>
            @else
                {{ $post->content }}
            @endif
        </div>
        @endif

        {{-- Inline edit form (hidden) --}}
        @if($post->alumni_id === $me->id && $post->content)
        <div id="post-edit-form-{{ $post->id }}" class="hidden px-4 pb-3">
            <textarea id="post-edit-input-{{ $post->id }}" rows="3" maxlength="1000"
                class="w-full bg-gray-50 dark:bg-gray-700 border border-primary/40 rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none transition">{{ $post->content }}</textarea>
            <div class="flex items-center gap-2 mt-2 justify-end">
                <button onclick="cancelEditPost({{ $post->id }})"
                    class="text-xs text-gray-400 hover:text-gray-600 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 transition">Cancel</button>
                <button onclick="saveEditPost({{ $post->id }})"
                    class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition font-semibold">Save</button>
            </div>
        </div>
        @endif

        {{-- Post photo --}}
        @if($post->photo_url)
        <div class="mt-1">
            <img src="{{ asset($post->photo_url) }}" alt="Post photo"
                 class="w-full max-h-96 object-cover">
        </div>
        @endif

        {{-- Reaction counts summary --}}
        <div class="px-4 pt-2 pb-1 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
            <button onclick="showReactors('post', {{ $post->id }}, 'like')"
                id="post-likes-summary-{{ $post->id }}"
                class="{{ $likes > 0 ? '' : 'hidden' }} hover:underline cursor-pointer transition">{{ $likes > 0 ? $likes : 0 }} 👍</button>
            <button onclick="showReactors('post', {{ $post->id }}, 'dislike')"
                id="post-dislikes-summary-{{ $post->id }}"
                class="{{ $dislikes > 0 ? '' : 'hidden' }} hover:underline cursor-pointer transition">{{ $dislikes > 0 ? $dislikes : 0 }} 👎</button>
            <span id="post-comment-count-{{ $post->id }}" class="{{ $commentCount > 0 ? '' : 'hidden' }}">{{ $commentCount > 0 ? $commentCount : 0 }} 💬</span>
        </div>

        {{-- Divider --}}
        <div class="mx-4 border-t border-gray-100 dark:border-gray-700"></div>

        {{-- Action bar --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
            {{-- Like --}}
            <button data-post="{{ $post->id }}" data-type="like"
                onclick="reactPost(this)"
                id="btn-like-{{ $post->id }}"
                class="flex items-center justify-center gap-1.5 py-2 text-sm font-medium transition hover:bg-gray-50 dark:hover:bg-gray-700 rounded-bl-2xl
                    {{ $userReaction === 'like' ? 'text-kgreen' : 'text-kgray dark:text-gray-400' }}">
                <i class="fa fa-thumbs-up"></i>
                <span id="post-like-count-{{ $post->id }}">{{ $likes > 0 ? $likes : '' }}</span>
                Like
            </button>

            {{-- Dislike --}}
            <button data-post="{{ $post->id }}" data-type="dislike"
                onclick="reactPost(this)"
                id="btn-dislike-{{ $post->id }}"
                class="flex items-center justify-center gap-1.5 py-2 text-sm font-medium transition hover:bg-gray-50 dark:hover:bg-gray-700
                    {{ $userReaction === 'dislike' ? 'text-primary' : 'text-kgray dark:text-gray-400' }}">
                <i class="fa fa-thumbs-down"></i>
                <span id="post-dislike-count-{{ $post->id }}">{{ $dislikes > 0 ? $dislikes : '' }}</span>
                Dislike
            </button>

            {{-- Comment toggle --}}
            <button onclick="toggleComments({{ $post->id }})"
                id="btn-comment-{{ $post->id }}"
                class="flex items-center justify-center gap-1.5 py-2 text-sm font-medium text-kgray dark:text-gray-400 transition hover:bg-gray-50 dark:hover:bg-gray-700 rounded-br-2xl">
                <i class="fa fa-comment"></i>
                <span id="btn-comment-count-{{ $post->id }}" class="{{ $commentCount > 0 ? 'font-bold' : 'hidden' }}">{{ $commentCount > 0 ? $commentCount : '' }}</span>
                Comment
            </button>
        </div>

        {{-- ============================================================
             COMMENTS SECTION
        ============================================================ --}}
        <div id="comments-{{ $post->id }}" class="hidden border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 rounded-b-2xl px-4 py-3">

            {{-- Existing comments --}}
            <div id="comment-list-{{ $post->id }}" class="space-y-3 mb-3">
                @foreach($post->comments as $comment)
                @php
                    $cPhoto = $comment->alumni->photo_url
                        ? (str_starts_with($comment->alumni->photo_url, 'uploads/')
                            ? asset($comment->alumni->photo_url)
                            : asset('storage/' . $comment->alumni->photo_url))
                        : null;
                    $cLikes    = $comment->reactions->where('type','like')->count();
                    $cDislikes = $comment->reactions->where('type','dislike')->count();
                    $cUserRxn  = $comment->reactions->where('alumni_id', $me->id)->first()?->type;
                @endphp

                <div id="comment-{{ $comment->id }}" class="flex gap-2.5">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        @if($cPhoto)
                            <img src="{{ $cPhoto }}" alt="{{ $comment->alumni->name }}"
                                 class="w-8 h-8 rounded-full object-cover ring-1 ring-primary/20">
                        @else
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($comment->alumni->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        {{-- Comment bubble --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl px-3 py-2 text-sm shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-semibold text-gray-800 dark:text-gray-100 text-xs">{{ $comment->alumni->name }}</span>
                                <div class="flex items-center gap-1">
                                    @if($comment->alumni_id === $me->id)
                                    <button onclick="toggleEditComment({{ $comment->id }})"
                                        class="text-gray-300 dark:text-gray-600 hover:text-blue-500 text-xs transition" title="Edit">
                                        <i class="fa fa-pen-to-square"></i>
                                    </button>
                                    @endif
                                    @if($comment->alumni_id === $me->id || $me->isAdmin())
                                    <button onclick="deleteComment({{ $comment->id }})"
                                        class="text-gray-300 dark:text-gray-600 hover:text-primary text-xs transition">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <p id="comment-text-{{ $comment->id }}" class="text-gray-700 dark:text-gray-200 mt-0.5 break-words">{{ $comment->content }}</p>
                        </div>

                        {{-- Inline comment edit form --}}
                        @if($comment->alumni_id === $me->id)
                        <div id="comment-edit-form-{{ $comment->id }}" class="hidden mt-1">
                            <textarea id="comment-edit-input-{{ $comment->id }}" rows="2" maxlength="500"
                                class="w-full bg-white dark:bg-gray-800 border border-primary/40 rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none transition">{{ $comment->content }}</textarea>
                            <div class="flex items-center gap-2 mt-1 justify-end">
                                <button onclick="cancelEditComment({{ $comment->id }})" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 transition">Cancel</button>
                                <button onclick="saveEditComment({{ $comment->id }})" class="text-xs bg-primary text-white px-2 py-1 rounded-lg hover:bg-red-700 transition font-semibold">Save</button>
                            </div>
                        </div>
                        @endif

                        {{-- Comment meta --}}
                        <div class="flex items-center gap-3 mt-1 px-1">
                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            <span id="comment-edited-{{ $comment->id }}" class="{{ $comment->updated_at->gt($comment->created_at) ? '' : 'hidden' }} text-xs text-gray-300 dark:text-gray-600 italic">(Edited)</span>
                            {{-- Like comment --}}
                            <button data-comment="{{ $comment->id }}" data-type="like"
                                onclick="reactComment(this)"
                                id="cbtn-like-{{ $comment->id }}"
                                class="text-xs font-medium transition
                                    {{ $cUserRxn === 'like' ? 'text-kgreen' : 'text-gray-400 hover:text-kgreen' }}">
                                <i class="fa fa-thumbs-up"></i>
                                <span id="c-like-count-{{ $comment->id }}">{{ $cLikes > 0 ? $cLikes : '' }}</span>
                            </button>
                            {{-- Dislike comment --}}
                            <button data-comment="{{ $comment->id }}" data-type="dislike"
                                onclick="reactComment(this)"
                                id="cbtn-dislike-{{ $comment->id }}"
                                class="text-xs font-medium transition
                                    {{ $cUserRxn === 'dislike' ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                                <i class="fa fa-thumbs-down"></i>
                                <span id="c-dislike-count-{{ $comment->id }}">{{ $cDislikes > 0 ? $cDislikes : '' }}</span>
                            </button>
                            {{-- Reply --}}
                            <button onclick="toggleReply({{ $comment->id }}, {{ $post->id }})"
                                class="text-xs text-gray-400 hover:text-primary transition font-medium">
                                Reply
                            </button>
                        </div>

                        {{-- Reply form (hidden) --}}
                        <div id="reply-form-{{ $comment->id }}" class="hidden mt-2">
                            <div class="flex gap-2">
                                <div class="flex-shrink-0">
                                    @if($myPhoto)
                                        <img src="{{ $myPhoto }}" class="w-7 h-7 rounded-full object-cover">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($me->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex gap-2">
                                    <input type="text"
                                        id="reply-input-{{ $comment->id }}"
                                        placeholder="Write a reply…"
                                        maxlength="500"
                                        class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                                    <button onclick="submitReply({{ $comment->id }}, {{ $post->id }})"
                                        class="bg-primary text-white text-xs px-3 py-1.5 rounded-xl hover:bg-red-700 transition">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Replies --}}
                        @if($comment->replies->isNotEmpty())
                        <div id="replies-{{ $comment->id }}" class="mt-2 ml-10 space-y-2">
                            @foreach($comment->replies as $reply)
                            @php
                                $rPhoto = $reply->alumni->photo_url
                                    ? (str_starts_with($reply->alumni->photo_url, 'uploads/')
                                        ? asset($reply->alumni->photo_url)
                                        : asset('storage/' . $reply->alumni->photo_url))
                                    : null;
                                $rLikes    = $reply->reactions->where('type','like')->count();
                                $rDislikes = $reply->reactions->where('type','dislike')->count();
                                $rUserRxn  = $reply->reactions->where('alumni_id', $me->id)->first()?->type;
                            @endphp
                            <div id="comment-{{ $reply->id }}" class="flex gap-2">
                                <div class="flex-shrink-0">
                                    @if($rPhoto)
                                        <img src="{{ $rPhoto }}" alt="{{ $reply->alumni->name }}"
                                             class="w-7 h-7 rounded-full object-cover ring-1 ring-primary/20">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($reply->alumni->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl px-3 py-2 text-sm shadow-sm border border-gray-100 dark:border-gray-700">
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-xs">{{ $reply->alumni->name }}</span>
                                            <div class="flex items-center gap-1">
                                                @if($reply->alumni_id === $me->id)
                                                <button onclick="toggleEditComment({{ $reply->id }})"
                                                    class="text-gray-300 dark:text-gray-600 hover:text-blue-500 text-xs transition" title="Edit">
                                                    <i class="fa fa-pen-to-square"></i>
                                                </button>
                                                @endif
                                                @if($reply->alumni_id === $me->id || $me->isAdmin())
                                                <button onclick="deleteComment({{ $reply->id }})"
                                                    class="text-gray-300 dark:text-gray-600 hover:text-primary text-xs transition">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <p id="comment-text-{{ $reply->id }}" class="text-gray-700 dark:text-gray-200 mt-0.5 break-words text-xs">{{ $reply->content }}</p>
                                    </div>
                                    @if($reply->alumni_id === $me->id)
                                    <div id="comment-edit-form-{{ $reply->id }}" class="hidden mt-1">
                                        <textarea id="comment-edit-input-{{ $reply->id }}" rows="2" maxlength="500"
                                            class="w-full bg-white dark:bg-gray-800 border border-primary/40 rounded-xl px-3 py-2 text-xs text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none transition">{{ $reply->content }}</textarea>
                                        <div class="flex items-center gap-2 mt-1 justify-end">
                                            <button onclick="cancelEditComment({{ $reply->id }})" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 transition">Cancel</button>
                                            <button onclick="saveEditComment({{ $reply->id }})" class="text-xs bg-primary text-white px-2 py-1 rounded-lg hover:bg-red-700 transition font-semibold">Save</button>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="flex items-center gap-3 mt-1 px-1">
                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        <span id="comment-edited-{{ $reply->id }}" class="{{ $reply->updated_at->gt($reply->created_at) ? '' : 'hidden' }} text-xs text-gray-300 dark:text-gray-600 italic">(Edited)</span>
                                        <button data-comment="{{ $reply->id }}" data-type="like"
                                            onclick="reactComment(this)"
                                            id="cbtn-like-{{ $reply->id }}"
                                            class="text-xs font-medium transition
                                                {{ $rUserRxn === 'like' ? 'text-kgreen' : 'text-gray-400 hover:text-kgreen' }}">
                                            <i class="fa fa-thumbs-up"></i>
                                            <span id="c-like-count-{{ $reply->id }}">{{ $rLikes > 0 ? $rLikes : '' }}</span>
                                        </button>
                                        <button data-comment="{{ $reply->id }}" data-type="dislike"
                                            onclick="reactComment(this)"
                                            id="cbtn-dislike-{{ $reply->id }}"
                                            class="text-xs font-medium transition
                                                {{ $rUserRxn === 'dislike' ? 'text-primary' : 'text-gray-400 hover:text-primary' }}">
                                            <i class="fa fa-thumbs-down"></i>
                                            <span id="c-dislike-count-{{ $reply->id }}">{{ $rDislikes > 0 ? $rDislikes : '' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div id="replies-{{ $comment->id }}" class="mt-2 ml-10 space-y-2"></div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>

            {{-- Add comment form --}}
            <div id="comment-form-{{ $post->id }}" class="flex gap-2.5">
                <div class="flex-shrink-0">
                    @if($myPhoto)
                        <img src="{{ $myPhoto }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr($me->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 flex gap-2">
                    <input type="text"
                        id="comment-input-{{ $post->id }}"
                        placeholder="Write a comment…"
                        maxlength="500"
                        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();submitComment({{ $post->id }});}"
                        class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                    <button onclick="submitComment({{ $post->id }})"
                        class="bg-primary text-white text-sm px-3 py-2 rounded-xl hover:bg-red-700 transition">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /post card --}}
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-12 text-center">
        <div class="text-5xl mb-4">🎓</div>
        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-2">No posts yet!</h3>
        <p class="text-sm text-gray-400 dark:text-gray-500">Be the first to share a memory with your batch.</p>
    </div>
    @endforelse
    </div>{{-- end #post-feed --}}

    {{-- Load More --}}
    @if($posts->hasMorePages())
    <div class="mt-5 text-center" id="loadMoreWrap">
        <button id="loadMoreBtn" onclick="loadMorePosts()"
            data-next="{{ $posts->nextPageUrl() }}"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium px-6 py-2.5 rounded-full hover:border-primary hover:text-primary transition">
            <i class="fa fa-chevron-down mr-1.5 text-xs"></i>Load More
        </button>
    </div>
    @endif

</div>

{{-- Reactor modal --}}
<div id="reactorsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" onclick="if(event.target===this)closeReactorsModal()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xs overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex gap-2">
                <button onclick="switchReactorTab('like')" id="reactor-tab-like"
                    class="text-sm font-semibold px-3 py-1 rounded-full transition bg-kgreen/10 text-kgreen">
                    👍 Liked <span id="reactor-like-count" class="text-xs font-normal opacity-70"></span>
                </button>
                <button onclick="switchReactorTab('dislike')" id="reactor-tab-dislike"
                    class="text-sm font-semibold px-3 py-1 rounded-full transition text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    👎 Disliked <span id="reactor-dislike-count" class="text-xs font-normal opacity-70"></span>
                </button>
            </div>
            <button onclick="closeReactorsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fa fa-times text-xs"></i>
            </button>
        </div>
        <div id="reactorsList" class="max-h-64 overflow-y-auto py-2">
            <div class="px-4 py-8 text-center text-sm text-gray-400">Loading…</div>
        </div>
    </div>
</div>

{{-- ====================================================================
     JAVASCRIPT
==================================================================== --}}
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ── Polling state ─────────────────────────────── */
let latestPostId = {{ $posts->first()?->id ?? 0 }};
let serverTime   = Math.floor(Date.now() / 1000);
const openPostIds    = new Set();
const seenCommentIds = new Set([
    @foreach($posts as $_post)
        @foreach($_post->comments as $_c)
            {{ $_c->id }},
            @foreach($_c->replies as $_r) {{ $_r->id }}, @endforeach
        @endforeach
    @endforeach
]);

let _pollTimer = null;

function _startPolling() { if (!_pollTimer) _pollTimer = setInterval(_pollForUpdates, 5000); }
function _stopPolling()  { clearInterval(_pollTimer); _pollTimer = null; }

document.addEventListener('visibilitychange', function() {
    document.hidden ? _stopPolling() : _startPolling();
});

async function _pollForUpdates() {
    try {
        const visiblePostIds = Array.from(document.querySelectorAll('[id^="post-"]'))
            .map(el => parseInt(el.id.replace('post-', ''))).filter(Boolean);
        const watchIds = visiblePostIds.filter(id => openPostIds.has(id));

        const params = new URLSearchParams({
            since:          serverTime,
            latest_post_id: latestPostId,
            post_ids:       watchIds.join(','),
        });
        const res  = await fetch('/wall/poll?' + params, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();

        serverTime = data.server_time;

        if (data.new_post_count > 0) {
            latestPostId = data.latest_post_id;
            const banner = document.getElementById('newPostsBanner');
            if (banner) {
                document.getElementById('newPostsCount').textContent = data.new_post_count;
                banner.classList.remove('hidden');
            }
        }

        for (const [postIdStr, comments] of Object.entries(data.new_comments || {})) {
            const list = document.getElementById('comment-list-' + postIdStr);
            if (!list) continue;
            for (const c of comments) {
                if (seenCommentIds.has(c.id)) continue;
                seenCommentIds.add(c.id);
                if (c.parent_id) {
                    const repliesEl = document.getElementById('replies-' + c.parent_id);
                    if (repliesEl) repliesEl.insertAdjacentHTML('beforeend', buildReplyHTML(c));
                } else {
                    list.insertAdjacentHTML('beforeend', buildCommentHTML(c, parseInt(postIdStr)));
                    updateCommentCountSummary(parseInt(postIdStr), 1);
                }
            }
        }

        if (typeof data.unread_notification_count !== 'undefined' && typeof updateNotifBadge === 'function') {
            updateNotifBadge(data.unread_notification_count);
        }
    } catch(e) { /* silent */ }
}

function loadNewPosts() { window.location.reload(); }

_startPolling();

/* ──────────────────────────────────────────────
   POST BUTTON STATE
────────────────────────────────────────────── */
function updatePostBtn() {
    const content   = document.getElementById('postContent').value.trim();
    const photoInput = document.getElementById('wallPhotoInput');
    const hasPhoto  = photoInput && photoInput.files && photoInput.files.length > 0;
    const btn       = document.getElementById('postSubmitBtn');
    const counter   = document.getElementById('charCounter');

    const len = document.getElementById('postContent').value.length;
    counter.textContent = len + ' / 1000';
    if (len > 900) counter.classList.add('text-primary');
    else counter.classList.remove('text-primary');

    btn.disabled = !(content || hasPhoto);
}

/* ──────────────────────────────────────────────
   PHOTO PREVIEW
────────────────────────────────────────────── */
function previewWallPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('photoPreview').src = e.target.result;
        document.getElementById('photoPreviewWrap').classList.remove('hidden');
        updatePostBtn();
    };
    reader.readAsDataURL(input.files[0]);
}

function clearWallPhoto() {
    document.getElementById('wallPhotoInput').value = '';
    document.getElementById('photoPreview').src = '';
    document.getElementById('photoPreviewWrap').classList.add('hidden');
    updatePostBtn();
}

/* ──────────────────────────────────────────────
   SEE MORE / SEE LESS
────────────────────────────────────────────── */
function expandPost(btn) {
    const card = btn.closest('[id^="post-"]');
    card.querySelector('.post-text-short').classList.add('hidden');
    card.querySelector('.post-text-full').classList.remove('hidden');
}
function collapsePost(btn) {
    const card = btn.closest('[id^="post-"]');
    card.querySelector('.post-text-full').classList.add('hidden');
    card.querySelector('.post-text-short').classList.remove('hidden');
}

/* ──────────────────────────────────────────────
   REACTIONS — POST
────────────────────────────────────────────── */
async function reactPost(btn) {
    const postId = btn.dataset.post;
    const type   = btn.dataset.type;
    try {
        const res = await fetch(`/posts/${postId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ type }),
        });
        const data = await res.json();

        // Update like button
        const likeBtn    = document.getElementById(`btn-like-${postId}`);
        const dislikeBtn = document.getElementById(`btn-dislike-${postId}`);
        const likeCount    = document.getElementById(`post-like-count-${postId}`);
        const dislikeCount = document.getElementById(`post-dislike-count-${postId}`);

        likeCount.textContent    = data.likes    > 0 ? data.likes    : '';
        dislikeCount.textContent = data.dislikes > 0 ? data.dislikes : '';

        if (data.user_reaction === 'like') {
            likeBtn.classList.add('text-kgreen');
            likeBtn.classList.remove('text-kgray', 'dark:text-gray-400');
            dislikeBtn.classList.remove('text-primary');
            dislikeBtn.classList.add('text-kgray', 'dark:text-gray-400');
        } else if (data.user_reaction === 'dislike') {
            dislikeBtn.classList.add('text-primary');
            dislikeBtn.classList.remove('text-kgray', 'dark:text-gray-400');
            likeBtn.classList.remove('text-kgreen');
            likeBtn.classList.add('text-kgray', 'dark:text-gray-400');
        } else {
            likeBtn.classList.remove('text-kgreen');
            likeBtn.classList.add('text-kgray', 'dark:text-gray-400');
            dislikeBtn.classList.remove('text-primary');
            dislikeBtn.classList.add('text-kgray', 'dark:text-gray-400');
        }

        // Update summary counts
        updateSummary(postId, data.likes, data.dislikes);
    } catch(e) {
        console.error('Reaction error:', e);
    }
}

function updateSummary(postId, likes, dislikes) {
    const likeEl    = document.getElementById(`post-likes-summary-${postId}`);
    const dislikeEl = document.getElementById(`post-dislikes-summary-${postId}`);
    if (likeEl) {
        likeEl.textContent = likes + ' 👍';
        likes > 0 ? likeEl.classList.remove('hidden') : likeEl.classList.add('hidden');
    }
    if (dislikeEl) {
        dislikeEl.textContent = dislikes + ' 👎';
        dislikes > 0 ? dislikeEl.classList.remove('hidden') : dislikeEl.classList.add('hidden');
    }
}

/* ──────────────────────────────────────────────
   REACTIONS — COMMENT
────────────────────────────────────────────── */
async function reactComment(btn) {
    const commentId = btn.dataset.comment;
    const type      = btn.dataset.type;
    try {
        const res = await fetch(`/comments/${commentId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ type }),
        });
        const data = await res.json();

        const likeBtn    = document.getElementById(`cbtn-like-${commentId}`);
        const dislikeBtn = document.getElementById(`cbtn-dislike-${commentId}`);
        const likeCount    = document.getElementById(`c-like-count-${commentId}`);
        const dislikeCount = document.getElementById(`c-dislike-count-${commentId}`);

        if (likeCount)    likeCount.textContent    = data.likes    > 0 ? data.likes    : '';
        if (dislikeCount) dislikeCount.textContent = data.dislikes > 0 ? data.dislikes : '';

        if (likeBtn && dislikeBtn) {
            if (data.user_reaction === 'like') {
                likeBtn.classList.add('text-kgreen');
                likeBtn.classList.remove('text-gray-400');
                dislikeBtn.classList.remove('text-primary');
                dislikeBtn.classList.add('text-gray-400');
            } else if (data.user_reaction === 'dislike') {
                dislikeBtn.classList.add('text-primary');
                dislikeBtn.classList.remove('text-gray-400');
                likeBtn.classList.remove('text-kgreen');
                likeBtn.classList.add('text-gray-400');
            } else {
                likeBtn.classList.remove('text-kgreen');
                likeBtn.classList.add('text-gray-400');
                dislikeBtn.classList.remove('text-primary');
                dislikeBtn.classList.add('text-gray-400');
            }
        }
    } catch(e) {
        console.error('Comment reaction error:', e);
    }
}

/* ──────────────────────────────────────────────
   TOGGLE COMMENTS
────────────────────────────────────────────── */
function toggleComments(postId) {
    const section = document.getElementById(`comments-${postId}`);
    const btn     = document.getElementById(`btn-comment-${postId}`);
    section.classList.toggle('hidden');
    if (!section.classList.contains('hidden')) {
        openPostIds.add(postId);
        btn.classList.add('text-primary');
        btn.classList.remove('text-kgray', 'dark:text-gray-400');
        document.getElementById(`comment-input-${postId}`).focus();
    } else {
        openPostIds.delete(postId);
        btn.classList.remove('text-primary');
        btn.classList.add('text-kgray', 'dark:text-gray-400');
    }
}

/* ──────────────────────────────────────────────
   DELETE POST
────────────────────────────────────────────── */
async function deletePost(postId) {
    if (!confirm('Delete this post? This cannot be undone.')) return;
    try {
        const res = await fetch(`/posts/${postId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (res.ok) {
            const card = document.getElementById(`post-${postId}`);
            if (card) {
                card.style.transition = 'opacity 0.4s, transform 0.4s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.97)';
                setTimeout(() => card.remove(), 400);
            }
        }
    } catch(e) {
        console.error('Delete post error:', e);
    }
}

/* ──────────────────────────────────────────────
   DELETE COMMENT
────────────────────────────────────────────── */
async function deleteComment(commentId) {
    if (!confirm('Delete this comment?')) return;
    try {
        const res = await fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (res.ok) {
            const el = document.getElementById(`comment-${commentId}`);
            if (el) {
                el.style.transition = 'opacity 0.3s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }
        }
    } catch(e) {
        console.error('Delete comment error:', e);
    }
}

/* ──────────────────────────────────────────────
   SUBMIT COMMENT
────────────────────────────────────────────── */
async function submitComment(postId) {
    const input   = document.getElementById(`comment-input-${postId}`);
    const content = input.value.trim();
    if (!content) return;

    try {
        const res = await fetch(`/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ content }),
        });
        const data = await res.json();
        if (data.success) {
            input.value = '';
            const c = data.comment;
            seenCommentIds.add(c.id);
            const html = buildCommentHTML(c, postId);
            const list = document.getElementById(`comment-list-${postId}`);
            list.insertAdjacentHTML('beforeend', html);
            updateCommentCountSummary(postId, 1);
        }
    } catch(e) {
        console.error('Submit comment error:', e);
    }
}

function buildCommentHTML(c, postId) {
    const avatarHtml = c.photo
        ? `<img src="${c.photo}" alt="${c.author}" class="w-8 h-8 rounded-full object-cover ring-1 ring-[#c0392b]/20">`
        : `<div class="w-8 h-8 rounded-full bg-[#c0392b] text-white flex items-center justify-center font-bold text-xs">${c.initial}</div>`;

    const editBtn = c.can_delete
        ? `<button onclick="toggleEditComment(${c.id})" class="text-gray-300 dark:text-gray-600 hover:text-blue-500 text-xs transition" title="Edit"><i class="fa fa-pen-to-square"></i></button>`
        : '';
    const deleteBtn = c.can_delete
        ? `<button onclick="deleteComment(${c.id})" class="text-gray-300 dark:text-gray-600 hover:text-[#c0392b] text-xs transition"><i class="fa fa-times"></i></button>`
        : '';
    const editForm = c.can_delete
        ? `<div id="comment-edit-form-${c.id}" class="hidden mt-1">
            <textarea id="comment-edit-input-${c.id}" rows="2" maxlength="500"
                class="w-full bg-white dark:bg-gray-800 border border-[#c0392b]/40 rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#c0392b]/40 resize-none transition">${c.content}</textarea>
            <div class="flex items-center gap-2 mt-1 justify-end">
                <button onclick="cancelEditComment(${c.id})" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 transition">Cancel</button>
                <button onclick="saveEditComment(${c.id})" class="text-xs bg-[#c0392b] text-white px-2 py-1 rounded-lg hover:bg-red-700 transition font-semibold">Save</button>
            </div>
        </div>`
        : '';

    return `
    <div id="comment-${c.id}" class="flex gap-2.5">
        <div class="flex-shrink-0">${avatarHtml}</div>
        <div class="flex-1 min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl px-3 py-2 text-sm shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-semibold text-gray-800 dark:text-gray-100 text-xs">${c.author}</span>
                    <div class="flex items-center gap-1">${editBtn}${deleteBtn}</div>
                </div>
                <p id="comment-text-${c.id}" class="text-gray-700 dark:text-gray-200 mt-0.5 break-words">${c.content}</p>
            </div>
            ${editForm}
            <div class="flex items-center gap-3 mt-1 px-1">
                <span class="text-xs text-gray-400">${c.time}</span>
                <span id="comment-edited-${c.id}" class="hidden text-xs text-gray-300 italic">(Edited)</span>
                <button data-comment="${c.id}" data-type="like" onclick="reactComment(this)"
                    id="cbtn-like-${c.id}"
                    class="text-xs font-medium text-gray-400 hover:text-[#27ae60] transition">
                    <i class="fa fa-thumbs-up"></i>
                    <span id="c-like-count-${c.id}"></span>
                </button>
                <button data-comment="${c.id}" data-type="dislike" onclick="reactComment(this)"
                    id="cbtn-dislike-${c.id}"
                    class="text-xs font-medium text-gray-400 hover:text-[#c0392b] transition">
                    <i class="fa fa-thumbs-down"></i>
                    <span id="c-dislike-count-${c.id}"></span>
                </button>
                <button onclick="toggleReply(${c.id}, ${postId})"
                    class="text-xs text-gray-400 hover:text-[#c0392b] transition font-medium">Reply</button>
            </div>
            <div id="reply-form-${c.id}" class="hidden mt-2">
                <div class="flex gap-2">
                    <div class="flex-shrink-0">${avatarHtml.replace('w-8 h-8', 'w-7 h-7').replace('text-xs', 'text-xs')}</div>
                    <div class="flex-1 flex gap-2">
                        <input type="text" id="reply-input-${c.id}" placeholder="Write a reply…" maxlength="500"
                            class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-1.5 text-xs text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#c0392b]/40 transition">
                        <button onclick="submitReply(${c.id}, ${postId})"
                            class="bg-[#c0392b] text-white text-xs px-3 py-1.5 rounded-xl hover:bg-red-700 transition">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="replies-${c.id}" class="mt-2 ml-10 space-y-2"></div>
        </div>
    </div>`;
}

function updateCommentCountSummary(postId, delta) {
    const el  = document.getElementById(`post-comment-count-${postId}`);
    const btn = document.getElementById(`btn-comment-count-${postId}`);
    if (!el) return;
    const current  = parseInt(el.textContent) || 0;
    const newCount = current + delta;
    el.textContent  = newCount + ' 💬';
    newCount > 0 ? el.classList.remove('hidden') : el.classList.add('hidden');
    if (btn) {
        btn.textContent = newCount > 0 ? newCount : '';
        newCount > 0 ? btn.classList.remove('hidden') : btn.classList.add('hidden');
        newCount > 0 ? btn.classList.add('font-bold') : btn.classList.remove('font-bold');
    }
}

/* ──────────────────────────────────────────────
   TOGGLE REPLY FORM
────────────────────────────────────────────── */
function toggleReply(commentId, postId) {
    const form = document.getElementById(`reply-form-${commentId}`);
    if (!form) return;
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        const input = document.getElementById(`reply-input-${commentId}`);
        if (input) input.focus();
    }
}

/* ──────────────────────────────────────────────
   SUBMIT REPLY
────────────────────────────────────────────── */
async function submitReply(commentId, postId) {
    const input   = document.getElementById(`reply-input-${commentId}`);
    const content = input ? input.value.trim() : '';
    if (!content) return;

    try {
        const res = await fetch(`/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ content, parent_id: commentId }),
        });
        const data = await res.json();
        if (data.success) {
            input.value = '';
            document.getElementById(`reply-form-${commentId}`).classList.add('hidden');
            const c = data.comment;
            seenCommentIds.add(c.id);
            const replyHtml = buildReplyHTML(c);
            const repliesContainer = document.getElementById(`replies-${commentId}`);
            if (repliesContainer) {
                repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
            }
            updateCommentCountSummary(postId, 1);
        }
    } catch(e) {
        console.error('Submit reply error:', e);
    }
}

function buildReplyHTML(c) {
    const avatarHtml = c.photo
        ? `<img src="${c.photo}" alt="${c.author}" class="w-7 h-7 rounded-full object-cover ring-1 ring-[#c0392b]/20">`
        : `<div class="w-7 h-7 rounded-full bg-[#c0392b] text-white flex items-center justify-center font-bold text-xs">${c.initial}</div>`;

    const editBtn = c.can_delete
        ? `<button onclick="toggleEditComment(${c.id})" class="text-gray-300 dark:text-gray-600 hover:text-blue-500 text-xs transition" title="Edit"><i class="fa fa-pen-to-square"></i></button>`
        : '';
    const deleteBtn = c.can_delete
        ? `<button onclick="deleteComment(${c.id})" class="text-gray-300 dark:text-gray-600 hover:text-[#c0392b] text-xs transition"><i class="fa fa-times"></i></button>`
        : '';
    const editForm = c.can_delete
        ? `<div id="comment-edit-form-${c.id}" class="hidden mt-1">
            <textarea id="comment-edit-input-${c.id}" rows="2" maxlength="500"
                class="w-full bg-white dark:bg-gray-800 border border-[#c0392b]/40 rounded-xl px-3 py-2 text-xs text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#c0392b]/40 resize-none transition">${c.content}</textarea>
            <div class="flex items-center gap-2 mt-1 justify-end">
                <button onclick="cancelEditComment(${c.id})" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 transition">Cancel</button>
                <button onclick="saveEditComment(${c.id})" class="text-xs bg-[#c0392b] text-white px-2 py-1 rounded-lg hover:bg-red-700 transition font-semibold">Save</button>
            </div>
        </div>`
        : '';

    return `
    <div id="comment-${c.id}" class="flex gap-2">
        <div class="flex-shrink-0">${avatarHtml}</div>
        <div class="flex-1 min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl px-3 py-2 text-sm shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-semibold text-gray-800 dark:text-gray-100 text-xs">${c.author}</span>
                    <div class="flex items-center gap-1">${editBtn}${deleteBtn}</div>
                </div>
                <p id="comment-text-${c.id}" class="text-gray-700 dark:text-gray-200 mt-0.5 break-words text-xs">${c.content}</p>
            </div>
            ${editForm}
            <div class="flex items-center gap-3 mt-1 px-1">
                <span class="text-xs text-gray-400">${c.time}</span>
                <span id="comment-edited-${c.id}" class="hidden text-xs text-gray-300 italic">(Edited)</span>
                <button data-comment="${c.id}" data-type="like" onclick="reactComment(this)"
                    id="cbtn-like-${c.id}"
                    class="text-xs font-medium text-gray-400 hover:text-[#27ae60] transition">
                    <i class="fa fa-thumbs-up"></i>
                    <span id="c-like-count-${c.id}"></span>
                </button>
                <button data-comment="${c.id}" data-type="dislike" onclick="reactComment(this)"
                    id="cbtn-dislike-${c.id}"
                    class="text-xs font-medium text-gray-400 hover:text-[#c0392b] transition">
                    <i class="fa fa-thumbs-down"></i>
                    <span id="c-dislike-count-${c.id}"></span>
                </button>
            </div>
        </div>
    </div>`;
}

/* ──────────────────────────────────────────────
   TAG AUTOCOMPLETE
────────────────────────────────────────────── */
let tagDebounce = null;
const taggedIds   = new Set();

let _tagAllCache = [];

function searchTags(q) {
    clearTimeout(tagDebounce);
    tagDebounce = setTimeout(async () => {
        try {
            const res = await fetch(`/wall/search-alumni?q=${encodeURIComponent(q)}`, {
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const people = await res.json();
            if (!q) _tagAllCache = people;
            showTagDropdown(people, q === '');
        } catch(e) {
            console.error('Tag search error:', e);
        }
    }, q.length === 0 ? 0 : 300);
}

function showTagDropdown(people, showTagAll = false) {
    const dd = document.getElementById('tagDropdown');
    const untagged = people.filter(p => !taggedIds.has(p.id));

    const tagAllBtn = (showTagAll && untagged.length > 0)
        ? `<button type="button" onclick="tagAllVisible()"
                class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left font-semibold text-primary hover:bg-primary/5 dark:hover:bg-primary/10 transition border-b border-gray-100 dark:border-gray-700">
                <i class="fa fa-users text-xs"></i> Tag All Classmates
           </button>`
        : '';

    if (!untagged.length && !tagAllBtn) {
        dd.innerHTML = '<div class="px-4 py-2.5 text-sm text-gray-400">No results found</div>';
        dd.classList.remove('hidden');
        return;
    }

    dd.innerHTML = tagAllBtn + untagged.map(p => {
        const avatar = p.photo
            ? `<img src="${p.photo}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">`
            : `<div class="w-7 h-7 rounded-full bg-[#c0392b] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">${p.name.charAt(0).toUpperCase()}</div>`;
        return `<button type="button" onclick='addTag(${JSON.stringify(p.id)}, ${JSON.stringify(p.name)})'
            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            ${avatar}
            <span class="text-gray-700 dark:text-gray-200">${p.name}</span>
        </button>`;
    }).join('');
    dd.classList.remove('hidden');
}

function tagAllVisible() {
    _tagAllCache.forEach(p => addTag(p.id, p.name));
    hideTagDropdown();
}

function hideTagDropdown() {
    document.getElementById('tagDropdown').classList.add('hidden');
}

function addTag(id, name) {
    if (taggedIds.has(id)) return;
    taggedIds.add(id);
    updateTaggedIdsInput();

    const chip = document.createElement('span');
    chip.id = `tag-chip-${id}`;
    chip.className = 'inline-flex items-center gap-1 bg-primary/10 dark:bg-primary/20 text-primary text-xs font-medium px-2 py-1 rounded-full';
    chip.innerHTML = `<i class="fa fa-at text-xs"></i>${name}<button type="button" onclick="removeTag(${id})" class="ml-0.5 hover:text-red-700 transition"><i class="fa fa-times text-xs"></i></button>`;
    document.getElementById('tagChips').appendChild(chip);

    document.getElementById('tagInput').value = '';
    hideTagDropdown();
    updatePostBtn();
}

function removeTag(id) {
    taggedIds.delete(id);
    updateTaggedIdsInput();
    const chip = document.getElementById(`tag-chip-${id}`);
    if (chip) chip.remove();
    updatePostBtn();
}

function updateTaggedIdsInput() {
    document.getElementById('taggedIds').value = Array.from(taggedIds).join(',');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('tagInputWrap');
    if (wrap && !wrap.contains(e.target)) hideTagDropdown();
    const ta = document.getElementById('postContent');
    const md = document.getElementById('mentionDropdown');
    if (md && ta && !md.contains(e.target) && e.target !== ta) hideMentionDropdown();
});

/* ──────────────────────────────────────────────
   INLINE @MENTION
────────────────────────────────────────────── */
let _mentionDebounce = null;
let _mentionMatch    = null;

function _getMentionAt(ta) {
    const before = ta.value.slice(0, ta.selectionStart);
    const m = before.match(/@([^\s@]*)$/);
    return m ? { query: m[1], start: ta.selectionStart - m[0].length, end: ta.selectionStart } : null;
}

function handleMentionInput(ta) {
    _mentionMatch = _getMentionAt(ta);
    clearTimeout(_mentionDebounce);
    if (!_mentionMatch) { hideMentionDropdown(); return; }
    _mentionDebounce = setTimeout(async () => {
        if (!_mentionMatch) return;
        try {
            const res = await fetch(`/wall/search-alumni?q=${encodeURIComponent(_mentionMatch.query)}`, {
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const people = await res.json();
            if (!people.length) { hideMentionDropdown(); return; }
            const dd = document.getElementById('mentionDropdown');
            if (!dd) return;
            dd.innerHTML = people.map(p => {
                const av = p.photo
                    ? `<img src="${p.photo}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">`
                    : `<div class="w-7 h-7 rounded-full bg-[#c0392b] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">${p.name.charAt(0).toUpperCase()}</div>`;
                return `<button type="button" onclick='pickMention(${JSON.stringify(p.id)}, ${JSON.stringify(p.name)})'
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    ${av}<span class="text-gray-700 dark:text-gray-200">${p.name}</span>
                </button>`;
            }).join('');
            dd.classList.remove('hidden');
        } catch(e) { hideMentionDropdown(); }
    }, 250);
}

function hideMentionDropdown() {
    const dd = document.getElementById('mentionDropdown');
    if (dd) dd.classList.add('hidden');
    _mentionMatch = null;
}

function pickMention(id, name) {
    const ta = document.getElementById('postContent');
    if (!ta || !_mentionMatch) return;
    const mention = '@' + name;
    ta.value = ta.value.slice(0, _mentionMatch.start) + mention + ' ' + ta.value.slice(_mentionMatch.end);
    ta.selectionStart = ta.selectionEnd = _mentionMatch.start + mention.length + 1;
    hideMentionDropdown();
    ta.focus();
    if (!taggedIds.has(id)) { taggedIds.add(id); updateTaggedIdsInput(); }
    updatePostBtn();
}

document.getElementById('postContent')?.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideMentionDropdown();
});

/* ──────────────────────────────────────────────
   EDIT POST
────────────────────────────────────────────── */
function toggleEditPost(postId) {
    const content = document.getElementById(`post-content-${postId}`);
    const form    = document.getElementById(`post-edit-form-${postId}`);
    if (!form) return;
    const hidden = form.classList.contains('hidden');
    if (hidden) {
        if (content) content.classList.add('hidden');
        form.classList.remove('hidden');
        document.getElementById(`post-edit-input-${postId}`)?.focus();
    } else {
        cancelEditPost(postId);
    }
}

function cancelEditPost(postId) {
    document.getElementById(`post-edit-form-${postId}`)?.classList.add('hidden');
    document.getElementById(`post-content-${postId}`)?.classList.remove('hidden');
}

async function saveEditPost(postId) {
    const input = document.getElementById(`post-edit-input-${postId}`);
    const text  = input ? input.value.trim() : '';
    if (!text) return;
    try {
        const res  = await fetch(`/posts/${postId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ content: text }),
        });
        const data = await res.json();
        if (data.success) {
            const el = document.getElementById(`post-content-${postId}`);
            if (el) el.textContent = data.content;
            if (data.edited) {
                const lbl = document.getElementById(`post-edited-label-${postId}`);
                if (lbl) lbl.classList.remove('hidden');
            }
            cancelEditPost(postId);
        }
    } catch(e) { console.error('Edit post error:', e); }
}

/* ──────────────────────────────────────────────
   SCROLL TO HIGHLIGHTED POST (from notification)
────────────────────────────────────────────── */
(function() {
    if (!window.location.hash) return;
    const targetId = window.location.hash.replace('#', '');
    const el = document.getElementById(targetId);
    if (!el) return;
    setTimeout(function() {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.style.transition = 'box-shadow 0.5s';
        el.style.boxShadow  = '0 0 0 3px #c0392b66';
        setTimeout(function() { el.style.boxShadow = ''; }, 2500);
    }, 400);
})();

/* ──────────────────────────────────────────────
   LOAD MORE / INFINITE SCROLL
────────────────────────────────────────────── */
let _loadingMore = false;

async function loadMorePosts() {
    const btn = document.getElementById('loadMoreBtn');
    if (!btn || _loadingMore) return;
    const nextUrl = btn.dataset.next;
    if (!nextUrl) return;

    _loadingMore = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1.5 text-xs"></i>Loading…';
    btn.disabled = true;

    try {
        const res  = await fetch(nextUrl, { headers: { 'Accept': 'text/html' } });
        const html = await res.text();
        const doc  = new DOMParser().parseFromString(html, 'text/html');

        const feed    = document.getElementById('post-feed');
        const newCards = doc.querySelectorAll('.post-card');
        newCards.forEach(card => feed.appendChild(card));

        // Track new comment IDs so poll doesn't double-insert them
        doc.querySelectorAll('[id^="comment-"]').forEach(el => {
            const cid = parseInt(el.id.replace('comment-', ''));
            if (cid) seenCommentIds.add(cid);
        });

        // Update Load More button with next-next page
        const nextBtn = doc.getElementById('loadMoreBtn');
        const wrap    = document.getElementById('loadMoreWrap');
        if (nextBtn && nextBtn.dataset.next) {
            btn.dataset.next = nextBtn.dataset.next;
            btn.innerHTML    = '<i class="fa fa-chevron-down mr-1.5 text-xs"></i>Load More';
            btn.disabled     = false;
        } else {
            if (wrap) wrap.remove();
        }
    } catch(e) {
        btn.innerHTML = '<i class="fa fa-chevron-down mr-1.5 text-xs"></i>Load More';
        btn.disabled  = false;
    }
    _loadingMore = false;
}

// Infinite scroll — auto-trigger Load More when near bottom
window.addEventListener('scroll', function() {
    if (_loadingMore) return;
    const btn = document.getElementById('loadMoreBtn');
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    if (rect.top < window.innerHeight + 200) loadMorePosts();
}, { passive: true });

/* ──────────────────────────────────────────────
   REACTOR MODAL (who liked / who disliked)
────────────────────────────────────────────── */
let _reactorData = { likes: [], dislikes: [] };
let _reactorTab  = 'like';

async function showReactors(modelType, modelId, tab) {
    _reactorTab = tab;
    const modal = document.getElementById('reactorsModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    document.getElementById('reactorsList').innerHTML = '<div class="px-4 py-8 text-center text-sm text-gray-400">Loading…</div>';

    const url = modelType === 'post' ? `/posts/${modelId}/reactions` : `/comments/${modelId}/reactions`;
    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        _reactorData = await res.json();
        _renderReactorList(tab);
    } catch(e) {
        document.getElementById('reactorsList').innerHTML = '<div class="px-4 py-4 text-center text-sm text-red-400">Failed to load</div>';
    }
}

function switchReactorTab(tab) {
    _reactorTab = tab;
    _renderReactorList(tab);
}

function _renderReactorList(tab) {
    const list       = _reactorData[tab === 'like' ? 'likes' : 'dislikes'] || [];
    const likeTab    = document.getElementById('reactor-tab-like');
    const dislikeTab = document.getElementById('reactor-tab-dislike');

    if (tab === 'like') {
        likeTab.className    = 'text-sm font-semibold px-3 py-1 rounded-full transition bg-[#27ae60]/10 text-[#27ae60]';
        dislikeTab.className = 'text-sm font-semibold px-3 py-1 rounded-full transition text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700';
    } else {
        dislikeTab.className = 'text-sm font-semibold px-3 py-1 rounded-full transition bg-[#c0392b]/10 text-[#c0392b]';
        likeTab.className    = 'text-sm font-semibold px-3 py-1 rounded-full transition text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700';
    }

    const likeCountEl    = document.getElementById('reactor-like-count');
    const dislikeCountEl = document.getElementById('reactor-dislike-count');
    if (likeCountEl)    likeCountEl.textContent    = _reactorData.likes.length    > 0 ? `(${_reactorData.likes.length})`    : '';
    if (dislikeCountEl) dislikeCountEl.textContent = _reactorData.dislikes.length > 0 ? `(${_reactorData.dislikes.length})` : '';

    const el = document.getElementById('reactorsList');
    if (!list.length) {
        el.innerHTML = `<div class="px-4 py-8 text-center text-sm text-gray-400">No ${tab === 'like' ? 'likes' : 'dislikes'} yet</div>`;
        return;
    }

    el.innerHTML = list.map(r => {
        const av = r.photo
            ? `<img src="${r.photo}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">`
            : `<div class="w-9 h-9 rounded-full bg-[#c0392b] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">${r.initial}</div>`;
        return `<div class="flex items-center gap-3 px-4 py-2.5">${av}<span class="text-sm text-gray-700 dark:text-gray-200">${r.name}</span></div>`;
    }).join('');
}

function closeReactorsModal() {
    const modal = document.getElementById('reactorsModal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeReactorsModal();
});

/* ──────────────────────────────────────────────
   EDIT COMMENT
────────────────────────────────────────────── */
function toggleEditComment(commentId) {
    const textEl = document.getElementById(`comment-text-${commentId}`);
    const formEl = document.getElementById(`comment-edit-form-${commentId}`);
    if (!formEl) return;
    if (formEl.classList.contains('hidden')) {
        if (textEl) textEl.classList.add('hidden');
        formEl.classList.remove('hidden');
        document.getElementById(`comment-edit-input-${commentId}`)?.focus();
    } else {
        cancelEditComment(commentId);
    }
}

function cancelEditComment(commentId) {
    document.getElementById(`comment-edit-form-${commentId}`)?.classList.add('hidden');
    document.getElementById(`comment-text-${commentId}`)?.classList.remove('hidden');
}

async function saveEditComment(commentId) {
    const input = document.getElementById(`comment-edit-input-${commentId}`);
    const text  = input ? input.value.trim() : '';
    if (!text) return;
    try {
        const res  = await fetch(`/comments/${commentId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ content: text }),
        });
        const data = await res.json();
        if (data.success) {
            const textEl = document.getElementById(`comment-text-${commentId}`);
            if (textEl) textEl.textContent = data.content;
            if (input) input.value = data.content;
            if (data.edited) {
                const editedEl = document.getElementById(`comment-edited-${commentId}`);
                if (editedEl) editedEl.classList.remove('hidden');
            }
            cancelEditComment(commentId);
        }
    } catch(e) { console.error('Edit comment error:', e); }
}
</script>

@endsection

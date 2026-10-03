@extends('layouts.app')
@section('title', 'ব্যাচ ওয়াল — KZS 2002')
@section('content')

@php
    $me = auth()->user();
    $myPhoto = $me->photo_url
        ? (str_starts_with($me->photo_url, 'uploads/') ? asset($me->photo_url) : asset('storage/' . $me->photo_url))
        : null;
    $myInitial = strtoupper(substr($me->name, 0, 1));
@endphp

<div class="m-main">
<div class="wrap">
<div class="col" style="margin-inline:auto">

{{-- Page heading --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px">
  <h1 class="m-title" style="font-size:22px">
    <i class="fa fa-users-line" style="margin-right:8px;color:var(--red-700)"></i>
    <span data-en="Batch Wall">ব্যাচ ওয়াল</span>
  </h1>
  <p class="when" data-en="Share memories with your batch">ব্যাচের সাথে স্মৃতি ভাগ করুন</p>
</div>

{{-- New posts banner (shown by polling JS) --}}
<div id="newPostsBanner" class="hidden" style="margin-bottom:16px;background:var(--tint);border:1px solid var(--tint-2);border-radius:14px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px">
  <span style="font-size:14px;color:var(--red-700);font-weight:600">
    <i class="fa fa-arrow-up" style="margin-right:6px"></i>
    <span id="newPostsCount">0</span> <span data-en="new post(s) — click to refresh">টি নতুন পোস্ট — রিফ্রেশ করুন</span>
  </span>
  <button onclick="loadNewPosts()" class="btn btn-primary btn-sm" data-en="Refresh">রিফ্রেশ</button>
</div>

{{-- ================================================================
     COMPOSER CARD
================================================================ --}}
<div class="panel" style="margin-bottom:18px">
  <form action="{{ route('wall.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
    @csrf
    <div style="display:flex;gap:12px">
      {{-- My avatar --}}
      @if($myPhoto)
        <img src="{{ $myPhoto }}" alt="{{ $me->name }}" class="avatar" style="flex-shrink:0">
      @else
        <div class="avatar" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-family:var(--f-display)">{{ $myInitial }}</div>
      @endif

      {{-- Composer body --}}
      <div style="flex:1;min-width:0">
        <div style="position:relative">
          <textarea id="postContent" name="content" rows="3"
            placeholder="মনে কী আছে, {{ $me->name }}? @ টাইপ করে কাউকে মেনশন করুন…"
            data-en-ph="What's on your mind, {{ $me->name }}? Use @ to mention someone…"
            maxlength="1000"
            oninput="updatePostBtn(); handleMentionInput(this)"
            style="width:100%;border-radius:14px;padding:10px 12px;font-size:15px;background:var(--bg)"></textarea>
          <div id="mentionDropdown" class="hidden" style="position:absolute;z-index:30;left:0;right:0;top:100%;margin-top:4px;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;max-height:200px;overflow-y:auto"></div>
        </div>

        {{-- Character counter --}}
        <div style="display:flex;justify-content:flex-end;margin:4px 0 8px">
          <span id="charCounter" class="when">0 / 1000</span>
        </div>

        {{-- Photo preview --}}
        <div id="photoPreviewWrap" class="hidden" style="margin-bottom:8px;position:relative;display:inline-block">
          <img id="photoPreview" src="" alt="Preview" style="height:120px;border-radius:12px;object-fit:cover;border:1px solid var(--line)">
          <button type="button" onclick="clearWallPhoto()"
            style="position:absolute;top:-8px;right:-8px;width:22px;height:22px;background:var(--red-700);color:#fff;border-radius:50%;border:0;cursor:pointer;font-size:11px;display:grid;place-items:center">
            <i class="fa fa-times"></i>
          </button>
        </div>

        {{-- Tag chips --}}
        <div id="tagChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px"></div>

        {{-- Hidden tagged_ids --}}
        <input type="hidden" name="tagged_ids" id="taggedIds" value="">

        {{-- Tag people input --}}
        <div style="position:relative;margin-bottom:12px" id="tagInputWrap">
          <div style="display:flex;align-items:center;gap:8px;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:8px 12px">
            <i class="fa fa-tag" style="color:var(--muted);font-size:12px"></i>
            <input type="text" id="tagInput" placeholder="ব্যাচমেট ট্যাগ করুন…"
              data-en-ph="Tag a batchmate…"
              autocomplete="off"
              oninput="searchTags(this.value)"
              onfocus="searchTags(this.value)"
              style="flex:1;background:transparent;border:0;padding:0;font-size:14px;color:var(--ink)">
          </div>
          <div id="tagDropdown" class="hidden" style="position:absolute;z-index:20;left:0;right:0;margin-top:4px;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);overflow:hidden"></div>
        </div>

        {{-- Action bar --}}
        <div style="display:flex;align-items:center;justify-content:space-between">
          <button type="button" onclick="document.getElementById('wallPhotoInput').click()" class="act">
            <i class="fa fa-image" style="color:var(--red-700)"></i>
            <span data-en="Photo">ছবি</span>
          </button>
          <input type="file" id="wallPhotoInput" name="photo" accept="image/*" class="hidden" onchange="previewWallPhoto(this)">
          <button type="submit" id="postSubmitBtn" class="btn btn-primary btn-sm" disabled>
            <i class="fa fa-paper-plane"></i> <span data-en="Post">পোস্ট করুন</span>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

{{-- Filter bar --}}
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
  <span class="when" style="margin-right:4px" data-en="Show:">দেখুন:</span>
  @foreach([
    'all'   => ['bn' => 'সব সময়',    'en' => 'All Time'],
    'month' => ['bn' => 'এই মাস',    'en' => 'This Month'],
    'week'  => ['bn' => 'এই সপ্তাহ', 'en' => 'This Week'],
  ] as $val => $labels)
  <a href="{{ route('wall.index', ['filter' => $val]) }}"
     data-en="{{ $labels['en'] }}"
     style="font-size:13px;padding:6px 14px;border-radius:999px;font-weight:600;border:1px solid;text-decoration:none;transition:background .15s;{{ $filter === $val ? 'background:var(--red-700);color:#fff;border-color:var(--red-700)' : 'background:#fff;color:var(--muted);border-color:var(--line)' }}">
    {{ $labels['bn'] }}
  </a>
  @endforeach
</div>

{{-- ================================================================
     FEED
================================================================ --}}
<div id="post-feed" class="feed">
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

<div id="post-{{ $post->id }}" class="post-card panel" style="padding:0;overflow:hidden">

  {{-- Post header --}}
  <div style="display:flex;gap:12px;align-items:flex-start;padding:16px 16px 8px">
    {{-- Author avatar --}}
    @if($authorPhoto)
      <img src="{{ $authorPhoto }}" alt="{{ $post->alumni->name }}" class="avatar" style="flex-shrink:0">
    @else
      <div class="avatar" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-family:var(--f-display)">{{ strtoupper(substr($post->alumni->name, 0, 1)) }}</div>
    @endif

    {{-- Author info --}}
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
        <div>
          <span class="who">{{ $post->alumni->name }}</span>
          @if($tagNames->isNotEmpty())
            <span class="when"> — {{ $tagNames->implode(', ') }}</span>
          @endif
          <div class="when" style="margin-top:2px;display:flex;align-items:center;gap:6px">
            {{ $post->created_at->diffForHumans() }}
            @if($post->updated_at->gt($post->created_at))
              <span id="post-edited-label-{{ $post->id }}" style="font-style:italic;font-size:12px">(সম্পাদিত)</span>
            @else
              <span id="post-edited-label-{{ $post->id }}" class="hidden" style="font-style:italic;font-size:12px">(সম্পাদিত)</span>
            @endif
          </div>
        </div>

        {{-- Edit / Delete (own post or admin) --}}
        @if($post->alumni_id === $me->id || $me->isAdmin())
        <div style="display:flex;gap:4px;align-items:center">
          @if($post->alumni_id === $me->id && $post->content)
          <button onclick="toggleEditPost({{ $post->id }})" class="icon-btn" title="Edit post" style="width:28px;height:28px">
            <i class="fa fa-pen-to-square" style="font-size:12px"></i>
          </button>
          @endif
          <button onclick="deletePost({{ $post->id }})" class="icon-btn" title="Delete post" style="width:28px;height:28px">
            <i class="fa fa-trash" style="font-size:12px"></i>
          </button>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Post content --}}
  @if($post->content)
  <div id="post-content-{{ $post->id }}" class="post-body" style="padding:2px 16px 10px">
    @if(strlen($post->content) > 200)
      <span class="post-text-short">{{ substr($post->content, 0, 200) }}<span style="color:var(--muted)">…</span>
        <button onclick="expandPost(this)" style="background:none;border:0;color:var(--red-700);font-size:13px;font-weight:600;cursor:pointer;padding:0 4px" data-en="See more">আরো দেখুন</button>
      </span>
      <span class="post-text-full hidden" data-full="{{ $post->content }}">{{ $post->content }}
        <button onclick="collapsePost(this)" style="background:none;border:0;color:var(--red-700);font-size:13px;font-weight:600;cursor:pointer;padding:0 4px" data-en="See less">কম দেখুন</button>
      </span>
    @else
      {{ $post->content }}
    @endif
  </div>
  @endif

  {{-- Inline edit form (hidden) --}}
  @if($post->alumni_id === $me->id && $post->content)
  <div id="post-edit-form-{{ $post->id }}" class="hidden" style="padding:0 16px 12px">
    <textarea id="post-edit-input-{{ $post->id }}" rows="3" maxlength="1000"
      style="width:100%;border-radius:12px;padding:10px 12px;font-size:14px;border-color:var(--red-700)">{{ $post->content }}</textarea>
    <div style="display:flex;gap:8px;margin-top:8px;justify-content:flex-end">
      <button onclick="cancelEditPost({{ $post->id }})" class="btn btn-ghost btn-sm" data-en="Cancel">বাতিল</button>
      <button onclick="saveEditPost({{ $post->id }})" class="btn btn-primary btn-sm" data-en="Save">সংরক্ষণ</button>
    </div>
  </div>
  @endif

  {{-- Post photo --}}
  @if($post->photo_url)
  <div style="margin-top:4px">
    <img src="{{ asset($post->photo_url) }}" alt="Post photo" style="width:100%;max-height:420px;object-fit:cover">
  </div>
  @endif

  {{-- Reaction counts summary --}}
  <div style="padding:8px 16px 4px;display:flex;align-items:center;gap:12px;font-size:13px;color:var(--muted)">
    <button onclick="showReactors('post', {{ $post->id }}, 'like')"
      id="post-likes-summary-{{ $post->id }}"
      class="{{ $likes > 0 ? '' : 'hidden' }}"
      style="background:none;border:0;cursor:pointer;color:var(--muted);font-size:13px;padding:0">{{ $likes > 0 ? $likes : 0 }} 👍</button>
    <button onclick="showReactors('post', {{ $post->id }}, 'dislike')"
      id="post-dislikes-summary-{{ $post->id }}"
      class="{{ $dislikes > 0 ? '' : 'hidden' }}"
      style="background:none;border:0;cursor:pointer;color:var(--muted);font-size:13px;padding:0">{{ $dislikes > 0 ? $dislikes : 0 }} 👎</button>
    <span id="post-comment-count-{{ $post->id }}" class="{{ $commentCount > 0 ? '' : 'hidden' }}">{{ $commentCount > 0 ? $commentCount : 0 }} 💬</span>
  </div>

  {{-- Action bar --}}
  <div class="actions" style="margin:0 12px;padding:4px 0">
    {{-- Like --}}
    <button data-post="{{ $post->id }}" data-type="like"
      onclick="reactPost(this)"
      id="btn-like-{{ $post->id }}"
      class="act {{ $userReaction === 'like' ? 'text-kgreen' : 'text-kgray' }}">
      <i class="fa fa-thumbs-up"></i>
      <span id="post-like-count-{{ $post->id }}">{{ $likes > 0 ? $likes : '' }}</span>
      <span data-en="Like">লাইক</span>
    </button>

    {{-- Dislike --}}
    <button data-post="{{ $post->id }}" data-type="dislike"
      onclick="reactPost(this)"
      id="btn-dislike-{{ $post->id }}"
      class="act {{ $userReaction === 'dislike' ? 'text-primary' : 'text-kgray' }}">
      <i class="fa fa-thumbs-down"></i>
      <span id="post-dislike-count-{{ $post->id }}">{{ $dislikes > 0 ? $dislikes : '' }}</span>
      <span data-en="Dislike">ডিসলাইক</span>
    </button>

    {{-- Comment toggle --}}
    <button onclick="toggleComments({{ $post->id }})"
      id="btn-comment-{{ $post->id }}"
      class="act text-kgray">
      <i class="fa fa-comment"></i>
      <span id="btn-comment-count-{{ $post->id }}" class="{{ $commentCount > 0 ? 'font-bold' : 'hidden' }}">{{ $commentCount > 0 ? $commentCount : '' }}</span>
      <span data-en="Comment">মন্তব্য</span>
    </button>
  </div>

  {{-- ============================================================
       COMMENTS SECTION
  ============================================================ --}}
  <div id="comments-{{ $post->id }}" class="hidden" style="border-top:1px solid var(--line);background:var(--bg);border-radius:0 0 20px 20px;padding:16px">

    {{-- Existing comments --}}
    <div id="comment-list-{{ $post->id }}" class="comments">
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

      <div id="comment-{{ $comment->id }}" class="cmt">
        {{-- Avatar --}}
        @if($cPhoto)
          <img src="{{ $cPhoto }}" alt="{{ $comment->alumni->name }}" class="avatar sm">
        @else
          <div class="avatar sm" style="background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:12px;font-family:var(--f-display)">{{ strtoupper(substr($comment->alumni->name, 0, 1)) }}</div>
        @endif

        <div style="flex:1;min-width:0">
          {{-- Comment bubble --}}
          <div class="bub">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
              <span class="who" style="font-size:13px">{{ $comment->alumni->name }}</span>
              <div style="display:flex;gap:2px">
                @if($comment->alumni_id === $me->id)
                <button onclick="toggleEditComment({{ $comment->id }})" class="icon-btn" style="width:22px;height:22px" title="Edit">
                  <i class="fa fa-pen-to-square" style="font-size:11px"></i>
                </button>
                @endif
                @if($comment->alumni_id === $me->id || $me->isAdmin())
                <button onclick="deleteComment({{ $comment->id }})" class="icon-btn" style="width:22px;height:22px">
                  <i class="fa fa-times" style="font-size:11px"></i>
                </button>
                @endif
              </div>
            </div>
            <p id="comment-text-{{ $comment->id }}" style="font-size:14px;margin:0">{{ $comment->content }}</p>
          </div>

          {{-- Inline comment edit form --}}
          @if($comment->alumni_id === $me->id)
          <div id="comment-edit-form-{{ $comment->id }}" class="hidden" style="margin-top:6px">
            <textarea id="comment-edit-input-{{ $comment->id }}" rows="2" maxlength="500"
              style="width:100%;border-radius:10px;padding:8px 10px;font-size:13px;border-color:var(--red-700)">{{ $comment->content }}</textarea>
            <div style="display:flex;gap:6px;margin-top:6px;justify-content:flex-end">
              <button onclick="cancelEditComment({{ $comment->id }})" class="btn btn-ghost btn-sm" style="font-size:12px;padding:4px 10px" data-en="Cancel">বাতিল</button>
              <button onclick="saveEditComment({{ $comment->id }})" class="btn btn-primary btn-sm" style="font-size:12px;padding:4px 10px" data-en="Save">সংরক্ষণ</button>
            </div>
          </div>
          @endif

          {{-- Comment meta --}}
          <div style="display:flex;align-items:center;gap:10px;margin-top:5px;padding-left:2px">
            <span class="when">{{ $comment->created_at->diffForHumans() }}</span>
            <span id="comment-edited-{{ $comment->id }}" class="{{ $comment->updated_at->gt($comment->created_at) ? '' : 'hidden' }}" style="font-size:12px;color:var(--muted);font-style:italic">(সম্পাদিত)</span>
            {{-- Like comment --}}
            <button data-comment="{{ $comment->id }}" data-type="like"
              onclick="reactComment(this)"
              id="cbtn-like-{{ $comment->id }}"
              class="{{ $cUserRxn === 'like' ? 'text-kgreen' : 'text-gray-400' }}"
              style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
              <i class="fa fa-thumbs-up"></i>
              <span id="c-like-count-{{ $comment->id }}">{{ $cLikes > 0 ? $cLikes : '' }}</span>
            </button>
            {{-- Dislike comment --}}
            <button data-comment="{{ $comment->id }}" data-type="dislike"
              onclick="reactComment(this)"
              id="cbtn-dislike-{{ $comment->id }}"
              class="{{ $cUserRxn === 'dislike' ? 'text-primary' : 'text-gray-400' }}"
              style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
              <i class="fa fa-thumbs-down"></i>
              <span id="c-dislike-count-{{ $comment->id }}">{{ $cDislikes > 0 ? $cDislikes : '' }}</span>
            </button>
            {{-- Reply --}}
            <button onclick="toggleReply({{ $comment->id }}, {{ $post->id }})"
              style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;color:var(--muted);padding:0"
              data-en="Reply">জবাব</button>
          </div>

          {{-- Reply form (hidden) --}}
          <div id="reply-form-{{ $comment->id }}" class="hidden" style="margin-top:8px">
            <div style="display:flex;gap:8px">
              @if($myPhoto)
                <img src="{{ $myPhoto }}" class="avatar sm" alt="" style="flex-shrink:0">
              @else
                <div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:11px;font-family:var(--f-display)">{{ $myInitial }}</div>
              @endif
              <div style="flex:1;display:flex;gap:8px">
                <input type="text"
                  id="reply-input-{{ $comment->id }}"
                  placeholder="জবাব লিখুন…"
                  maxlength="500"
                  style="flex:1;min-width:0;border-radius:10px;padding:6px 10px;font-size:13px">
                <button onclick="submitReply({{ $comment->id }}, {{ $post->id }})" class="btn btn-primary btn-sm" style="padding:6px 12px;flex-shrink:0">
                  <i class="fa fa-paper-plane"></i>
                </button>
              </div>
            </div>
          </div>

          {{-- Replies --}}
          @if($comment->replies->isNotEmpty())
          <div id="replies-{{ $comment->id }}" style="margin-top:8px;margin-left:38px;display:grid;gap:8px">
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
            <div id="comment-{{ $reply->id }}" class="cmt">
              @if($rPhoto)
                <img src="{{ $rPhoto }}" alt="{{ $reply->alumni->name }}" class="avatar sm">
              @else
                <div class="avatar sm" style="background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:11px;font-family:var(--f-display)">{{ strtoupper(substr($reply->alumni->name, 0, 1)) }}</div>
              @endif
              <div style="flex:1;min-width:0">
                <div class="bub">
                  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
                    <span class="who" style="font-size:12px">{{ $reply->alumni->name }}</span>
                    <div style="display:flex;gap:2px">
                      @if($reply->alumni_id === $me->id)
                      <button onclick="toggleEditComment({{ $reply->id }})" class="icon-btn" style="width:20px;height:20px" title="Edit">
                        <i class="fa fa-pen-to-square" style="font-size:10px"></i>
                      </button>
                      @endif
                      @if($reply->alumni_id === $me->id || $me->isAdmin())
                      <button onclick="deleteComment({{ $reply->id }})" class="icon-btn" style="width:20px;height:20px">
                        <i class="fa fa-times" style="font-size:10px"></i>
                      </button>
                      @endif
                    </div>
                  </div>
                  <p id="comment-text-{{ $reply->id }}" style="font-size:13px;margin:0">{{ $reply->content }}</p>
                </div>
                @if($reply->alumni_id === $me->id)
                <div id="comment-edit-form-{{ $reply->id }}" class="hidden" style="margin-top:6px">
                  <textarea id="comment-edit-input-{{ $reply->id }}" rows="2" maxlength="500"
                    style="width:100%;border-radius:10px;padding:8px 10px;font-size:12px;border-color:var(--red-700)">{{ $reply->content }}</textarea>
                  <div style="display:flex;gap:6px;margin-top:4px;justify-content:flex-end">
                    <button onclick="cancelEditComment({{ $reply->id }})" class="btn btn-ghost btn-sm" style="font-size:11px;padding:3px 8px" data-en="Cancel">বাতিল</button>
                    <button onclick="saveEditComment({{ $reply->id }})" class="btn btn-primary btn-sm" style="font-size:11px;padding:3px 8px" data-en="Save">সংরক্ষণ</button>
                  </div>
                </div>
                @endif
                <div style="display:flex;align-items:center;gap:10px;margin-top:5px;padding-left:2px">
                  <span class="when">{{ $reply->created_at->diffForHumans() }}</span>
                  <span id="comment-edited-{{ $reply->id }}" class="{{ $reply->updated_at->gt($reply->created_at) ? '' : 'hidden' }}" style="font-size:12px;color:var(--muted);font-style:italic">(সম্পাদিত)</span>
                  <button data-comment="{{ $reply->id }}" data-type="like"
                    onclick="reactComment(this)"
                    id="cbtn-like-{{ $reply->id }}"
                    class="{{ $rUserRxn === 'like' ? 'text-kgreen' : 'text-gray-400' }}"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
                    <i class="fa fa-thumbs-up"></i>
                    <span id="c-like-count-{{ $reply->id }}">{{ $rLikes > 0 ? $rLikes : '' }}</span>
                  </button>
                  <button data-comment="{{ $reply->id }}" data-type="dislike"
                    onclick="reactComment(this)"
                    id="cbtn-dislike-{{ $reply->id }}"
                    class="{{ $rUserRxn === 'dislike' ? 'text-primary' : 'text-gray-400' }}"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
                    <i class="fa fa-thumbs-down"></i>
                    <span id="c-dislike-count-{{ $reply->id }}">{{ $rDislikes > 0 ? $rDislikes : '' }}</span>
                  </button>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @else
          <div id="replies-{{ $comment->id }}" style="margin-top:8px;margin-left:38px;display:grid;gap:8px"></div>
          @endif

        </div>
      </div>
      @endforeach
    </div>

    {{-- Add comment form --}}
    <div id="comment-form-{{ $post->id }}" class="cform" style="margin-top:12px">
      @if($myPhoto)
        <img src="{{ $myPhoto }}" class="avatar sm" alt="" style="flex-shrink:0">
      @else
        <div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:12px;font-family:var(--f-display)">{{ $myInitial }}</div>
      @endif
      <input type="text"
        id="comment-input-{{ $post->id }}"
        placeholder="মন্তব্য লিখুন…"
        data-en-ph="Add a comment…"
        maxlength="500"
        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();submitComment({{ $post->id }});}"
        style="flex:1;border-radius:12px;padding:8px 12px;font-size:14px">
      <button onclick="submitComment({{ $post->id }})" class="btn btn-primary btn-sm" style="padding:8px 14px;flex-shrink:0">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>
  </div>

</div>{{-- /post card --}}
@empty
<div class="panel" style="text-align:center;padding:48px 24px">
  <div style="font-size:48px;margin-bottom:16px">🎓</div>
  <h3 style="font-size:18px;font-weight:600;color:var(--muted);margin-bottom:8px" data-en="No posts yet!">এখনো কোনো পোস্ট নেই!</h3>
  <p style="font-size:14px;color:var(--muted)" data-en="Be the first to share a memory with your batch.">প্রথম হয়ে ব্যাচের সাথে একটি স্মৃতি শেয়ার করুন।</p>
</div>
@endforelse
</div>{{-- end #post-feed --}}

{{-- Load More --}}
@if($posts->hasMorePages())
<div id="loadMoreWrap" style="margin-top:20px;text-align:center">
  <button id="loadMoreBtn" onclick="loadMorePosts()"
    data-next="{{ $posts->nextPageUrl() }}"
    class="btn btn-ghost">
    <i class="fa fa-chevron-down" style="font-size:12px;margin-right:4px"></i>
    <span data-en="Load More">আরো দেখুন</span>
  </button>
</div>
@endif

</div>{{-- /col --}}
</div>{{-- /wrap --}}
</div>{{-- /m-main --}}

{{-- Reactor modal --}}
<div id="reactorsModal" class="hidden" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.4)" onclick="if(event.target===this)closeReactorsModal()">
  <div class="panel" style="width:100%;max-width:320px;padding:0;overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--line)">
      <div style="display:flex;gap:8px">
        <button onclick="switchReactorTab('like')" id="reactor-tab-like"
          style="font-size:13px;font-weight:600;padding:5px 12px;border-radius:999px;border:0;cursor:pointer;background:rgba(22,163,74,.1);color:#16a34a">
          👍 <span data-en="Liked">লাইক করেছেন</span> <span id="reactor-like-count" style="font-size:11px;opacity:.7"></span>
        </button>
        <button onclick="switchReactorTab('dislike')" id="reactor-tab-dislike"
          style="font-size:13px;font-weight:600;padding:5px 12px;border-radius:999px;border:0;cursor:pointer;background:transparent;color:var(--muted)">
          👎 <span data-en="Disliked">ডিসলাইক করেছেন</span> <span id="reactor-dislike-count" style="font-size:11px;opacity:.7"></span>
        </button>
      </div>
      <button onclick="closeReactorsModal()" class="icon-btn" style="width:28px;height:28px">
        <i class="fa fa-times" style="font-size:12px"></i>
      </button>
    </div>
    <div id="reactorsList" style="max-height:260px;overflow-y:auto;padding:8px 0">
      <div style="padding:32px 16px;text-align:center;font-size:14px;color:var(--muted)" data-en="Loading…">লোড হচ্ছে…</div>
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
    if (len > 900) counter.style.color = 'var(--red-700)';
    else counter.style.color = '';

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

        const likeBtn    = document.getElementById(`btn-like-${postId}`);
        const dislikeBtn = document.getElementById(`btn-dislike-${postId}`);
        const likeCount    = document.getElementById(`post-like-count-${postId}`);
        const dislikeCount = document.getElementById(`post-dislike-count-${postId}`);

        likeCount.textContent    = data.likes    > 0 ? data.likes    : '';
        dislikeCount.textContent = data.dislikes > 0 ? data.dislikes : '';

        if (data.user_reaction === 'like') {
            likeBtn.classList.add('text-kgreen');
            likeBtn.classList.remove('text-kgray');
            dislikeBtn.classList.remove('text-primary');
            dislikeBtn.classList.add('text-kgray');
        } else if (data.user_reaction === 'dislike') {
            dislikeBtn.classList.add('text-primary');
            dislikeBtn.classList.remove('text-kgray');
            likeBtn.classList.remove('text-kgreen');
            likeBtn.classList.add('text-kgray');
        } else {
            likeBtn.classList.remove('text-kgreen');
            likeBtn.classList.add('text-kgray');
            dislikeBtn.classList.remove('text-primary');
            dislikeBtn.classList.add('text-kgray');
        }

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
        btn.classList.remove('text-kgray');
        document.getElementById(`comment-input-${postId}`).focus();
    } else {
        openPostIds.delete(postId);
        btn.classList.remove('text-primary');
        btn.classList.add('text-kgray');
    }
}

/* ──────────────────────────────────────────────
   DELETE POST
────────────────────────────────────────────── */
async function deletePost(postId) {
    if (!confirm('এই পোস্টটি মুছে ফেলবেন? এটি আর পুনরুদ্ধার করা যাবে না।')) return;
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
    if (!confirm('এই মন্তব্যটি মুছে ফেলবেন?')) return;
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
        ? `<img src="${c.photo}" alt="${c.author}" class="avatar sm" style="flex-shrink:0">`
        : `<div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:12px;font-family:var(--f-display)">${c.initial}</div>`;

    const editBtn = c.can_delete
        ? `<button onclick="toggleEditComment(${c.id})" class="icon-btn" style="width:22px;height:22px" title="Edit"><i class="fa fa-pen-to-square" style="font-size:11px"></i></button>`
        : '';
    const deleteBtn = c.can_delete
        ? `<button onclick="deleteComment(${c.id})" class="icon-btn" style="width:22px;height:22px"><i class="fa fa-times" style="font-size:11px"></i></button>`
        : '';
    const editForm = c.can_delete
        ? `<div id="comment-edit-form-${c.id}" class="hidden" style="margin-top:6px">
            <textarea id="comment-edit-input-${c.id}" rows="2" maxlength="500"
                style="width:100%;border-radius:10px;padding:8px 10px;font-size:13px;border-color:var(--red-700)">${c.content}</textarea>
            <div style="display:flex;gap:6px;margin-top:6px;justify-content:flex-end">
                <button onclick="cancelEditComment(${c.id})" class="btn btn-ghost btn-sm" style="font-size:12px;padding:4px 10px">Cancel</button>
                <button onclick="saveEditComment(${c.id})" class="btn btn-primary btn-sm" style="font-size:12px;padding:4px 10px">Save</button>
            </div>
        </div>`
        : '';

    return `
    <div id="comment-${c.id}" class="cmt">
        <div style="flex-shrink:0">${avatarHtml}</div>
        <div style="flex:1;min-width:0">
            <div class="bub">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
                    <span class="who" style="font-size:13px">${c.author}</span>
                    <div style="display:flex;gap:2px">${editBtn}${deleteBtn}</div>
                </div>
                <p id="comment-text-${c.id}" style="font-size:14px;margin:0">${c.content}</p>
            </div>
            ${editForm}
            <div style="display:flex;align-items:center;gap:10px;margin-top:5px;padding-left:2px">
                <span class="when">${c.time}</span>
                <span id="comment-edited-${c.id}" class="hidden" style="font-size:12px;color:var(--muted);font-style:italic">(Edited)</span>
                <button data-comment="${c.id}" data-type="like" onclick="reactComment(this)"
                    id="cbtn-like-${c.id}"
                    class="text-gray-400"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
                    <i class="fa fa-thumbs-up"></i>
                    <span id="c-like-count-${c.id}"></span>
                </button>
                <button data-comment="${c.id}" data-type="dislike" onclick="reactComment(this)"
                    id="cbtn-dislike-${c.id}"
                    class="text-gray-400"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
                    <i class="fa fa-thumbs-down"></i>
                    <span id="c-dislike-count-${c.id}"></span>
                </button>
                <button onclick="toggleReply(${c.id}, ${postId})"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;color:var(--muted);padding:0">Reply</button>
            </div>
            <div id="reply-form-${c.id}" class="hidden" style="margin-top:8px">
                <div style="display:flex;gap:8px">
                    <div style="flex-shrink:0">${avatarHtml}</div>
                    <div style="flex:1;display:flex;gap:8px">
                        <input type="text" id="reply-input-${c.id}" placeholder="Write a reply…" maxlength="500"
                            style="flex:1;min-width:0;border-radius:10px;padding:6px 10px;font-size:13px">
                        <button onclick="submitReply(${c.id}, ${postId})" class="btn btn-primary btn-sm" style="padding:6px 12px;flex-shrink:0">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="replies-${c.id}" style="margin-top:8px;margin-left:38px;display:grid;gap:8px"></div>
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
        ? `<img src="${c.photo}" alt="${c.author}" class="avatar sm" style="flex-shrink:0">`
        : `<div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:11px;font-family:var(--f-display)">${c.initial}</div>`;

    const editBtn = c.can_delete
        ? `<button onclick="toggleEditComment(${c.id})" class="icon-btn" style="width:20px;height:20px" title="Edit"><i class="fa fa-pen-to-square" style="font-size:10px"></i></button>`
        : '';
    const deleteBtn = c.can_delete
        ? `<button onclick="deleteComment(${c.id})" class="icon-btn" style="width:20px;height:20px"><i class="fa fa-times" style="font-size:10px"></i></button>`
        : '';
    const editForm = c.can_delete
        ? `<div id="comment-edit-form-${c.id}" class="hidden" style="margin-top:6px">
            <textarea id="comment-edit-input-${c.id}" rows="2" maxlength="500"
                style="width:100%;border-radius:10px;padding:8px 10px;font-size:12px;border-color:var(--red-700)">${c.content}</textarea>
            <div style="display:flex;gap:6px;margin-top:4px;justify-content:flex-end">
                <button onclick="cancelEditComment(${c.id})" class="btn btn-ghost btn-sm" style="font-size:11px;padding:3px 8px">Cancel</button>
                <button onclick="saveEditComment(${c.id})" class="btn btn-primary btn-sm" style="font-size:11px;padding:3px 8px">Save</button>
            </div>
        </div>`
        : '';

    return `
    <div id="comment-${c.id}" class="cmt">
        <div style="flex-shrink:0">${avatarHtml}</div>
        <div style="flex:1;min-width:0">
            <div class="bub">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
                    <span class="who" style="font-size:12px">${c.author}</span>
                    <div style="display:flex;gap:2px">${editBtn}${deleteBtn}</div>
                </div>
                <p id="comment-text-${c.id}" style="font-size:13px;margin:0">${c.content}</p>
            </div>
            ${editForm}
            <div style="display:flex;align-items:center;gap:10px;margin-top:5px;padding-left:2px">
                <span class="when">${c.time}</span>
                <span id="comment-edited-${c.id}" class="hidden" style="font-size:12px;color:var(--muted);font-style:italic">(Edited)</span>
                <button data-comment="${c.id}" data-type="like" onclick="reactComment(this)"
                    id="cbtn-like-${c.id}"
                    class="text-gray-400"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
                    <i class="fa fa-thumbs-up"></i>
                    <span id="c-like-count-${c.id}"></span>
                </button>
                <button data-comment="${c.id}" data-type="dislike" onclick="reactComment(this)"
                    id="cbtn-dislike-${c.id}"
                    class="text-gray-400"
                    style="background:none;border:0;cursor:pointer;font-size:12px;font-weight:600;padding:0">
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
                style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;font-size:14px;font-weight:600;color:var(--red-700);background:none;border:0;border-bottom:1px solid var(--line);cursor:pointer;text-align:left">
                <i class="fa fa-users" style="font-size:12px"></i> সব ব্যাচমেট ট্যাগ করুন
           </button>`
        : '';

    if (!untagged.length && !tagAllBtn) {
        dd.innerHTML = '<div style="padding:10px 12px;font-size:14px;color:var(--muted)">কেউ পাওয়া যায়নি</div>';
        dd.classList.remove('hidden');
        return;
    }

    dd.innerHTML = tagAllBtn + untagged.map(p => {
        const avatar = p.photo
            ? `<img src="${p.photo}" class="avatar sm" style="flex-shrink:0" alt="">`
            : `<div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:11px;font-family:var(--f-display)">${p.name.charAt(0).toUpperCase()}</div>`;
        return `<button type="button" onclick='addTag(${JSON.stringify(p.id)}, ${JSON.stringify(p.name)})'
            style="width:100%;display:flex;align-items:center;gap:10px;padding:8px 12px;font-size:14px;color:var(--ink);background:none;border:0;cursor:pointer;text-align:left;transition:background .1s"
            onmouseover="this.style.background='var(--tint)'" onmouseout="this.style.background='none'">
            ${avatar}
            <span>${p.name}</span>
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
    chip.style.cssText = 'display:inline-flex;align-items:center;gap:4px;background:var(--tint);color:var(--red-700);font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;border:1px solid var(--tint-2)';
    chip.innerHTML = `<i class="fa fa-at" style="font-size:10px"></i>${name}<button type="button" onclick="removeTag(${id})" style="background:none;border:0;cursor:pointer;color:var(--red-700);margin-left:2px;line-height:1"><i class="fa fa-times" style="font-size:10px"></i></button>`;
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
                    ? `<img src="${p.photo}" class="avatar sm" style="flex-shrink:0" alt="">`
                    : `<div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:11px;font-family:var(--f-display)">${p.name.charAt(0).toUpperCase()}</div>`;
                return `<button type="button" onclick='pickMention(${JSON.stringify(p.id)}, ${JSON.stringify(p.name)})'
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:8px 12px;font-size:14px;color:var(--ink);background:none;border:0;cursor:pointer;text-align:left;transition:background .1s"
                    onmouseover="this.style.background='var(--tint)'" onmouseout="this.style.background='none'">
                    ${av}<span>${p.name}</span>
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
        el.style.boxShadow  = '0 0 0 3px rgba(196,18,26,.4)';
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
    btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right:6px"></i>লোড হচ্ছে…';
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
            btn.innerHTML    = '<i class="fa fa-chevron-down" style="font-size:12px;margin-right:4px"></i>আরো দেখুন';
            btn.disabled     = false;
        } else {
            if (wrap) wrap.remove();
        }
    } catch(e) {
        btn.innerHTML = '<i class="fa fa-chevron-down" style="font-size:12px;margin-right:4px"></i>আরো দেখুন';
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
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.getElementById('reactorsList').innerHTML = '<div style="padding:32px 16px;text-align:center;font-size:14px;color:var(--muted)">লোড হচ্ছে…</div>';

    const url = modelType === 'post' ? `/posts/${modelId}/reactions` : `/comments/${modelId}/reactions`;
    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        _reactorData = await res.json();
        _renderReactorList(tab);
    } catch(e) {
        document.getElementById('reactorsList').innerHTML = '<div style="padding:16px;text-align:center;font-size:14px;color:var(--red-700)">লোড ব্যর্থ হয়েছে</div>';
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
        likeTab.style.background    = 'rgba(22,163,74,.12)';
        likeTab.style.color         = '#16a34a';
        dislikeTab.style.background = 'transparent';
        dislikeTab.style.color      = 'var(--muted)';
    } else {
        dislikeTab.style.background = 'rgba(196,18,26,.12)';
        dislikeTab.style.color      = 'var(--red-700)';
        likeTab.style.background    = 'transparent';
        likeTab.style.color         = 'var(--muted)';
    }

    const likeCountEl    = document.getElementById('reactor-like-count');
    const dislikeCountEl = document.getElementById('reactor-dislike-count');
    if (likeCountEl)    likeCountEl.textContent    = _reactorData.likes.length    > 0 ? `(${_reactorData.likes.length})`    : '';
    if (dislikeCountEl) dislikeCountEl.textContent = _reactorData.dislikes.length > 0 ? `(${_reactorData.dislikes.length})` : '';

    const el = document.getElementById('reactorsList');
    if (!list.length) {
        el.innerHTML = `<div style="padding:32px 16px;text-align:center;font-size:14px;color:var(--muted)">কোনো ${tab === 'like' ? 'লাইক' : 'ডিসলাইক'} নেই</div>`;
        return;
    }

    el.innerHTML = list.map(r => {
        const av = r.photo
            ? `<img src="${r.photo}" class="avatar sm" style="flex-shrink:0" alt="">`
            : `<div class="avatar sm" style="flex-shrink:0;background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:12px;font-family:var(--f-display)">${r.initial}</div>`;
        return `<div style="display:flex;align-items:center;gap:12px;padding:10px 16px">${av}<span style="font-size:14px;color:var(--ink)">${r.name}</span></div>`;
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

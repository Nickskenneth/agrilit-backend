<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForumPostResource;
use App\Http\Resources\ForumReplyResource;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    use ApiResponseTrait;

    // ============================================================
    // INDEX — List semua post yang sudah approved
    // GET /api/forum
    // Query params:
    //   ?commodity=cabai
    //   ?filter=unanswered   → hanya yang belum dijawab
    //   ?filter=answered     → hanya yang sudah dijawab
    //   ?search=tungau
    //   ?per_page=10
    // ============================================================
    public function index(Request $request)
    {
        $query = ForumPost::approved()
                          ->with(['user', 'expertReply.user'])
                          ->orderBy('created_at', 'desc');

        if ($request->filled('commodity')) {
            $query->forCommodity($request->commodity);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if ($request->filter === 'unanswered') {
            $query->unanswered();
        } elseif ($request->filter === 'answered') {
            $query->where('is_answered', true);
        }

        $perPage = min($request->get('per_page', 10), 50);
        $posts   = $query->paginate($perPage);

        return $this->paginatedResponse(
            $posts->through(fn($post) => new ForumPostResource($post))
        );
    }

    // ============================================================
    // SHOW — Detail satu post + semua replies
    // GET /api/forum/{id}
    // ============================================================
    public function show(string $id)
    {
        $post = ForumPost::approved()
                         ->with([
                             'user',
                             'replies' => fn($q) => $q->with('user')->orderBy('is_expert_answer', 'desc')->orderBy('upvotes', 'desc'),
                             'expertReply.user',
                         ])
                         ->findOrFail($id);

        // Increment view
        ForumPost::where('id', $id)->increment('views');

        return $this->successResponse(new ForumPostResource($post));
    }

    // ============================================================
    // STORE — Petani buat post baru
    // POST /api/forum
    // ============================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string|min:20',
            'commodity' => 'required|in:cabai,kentang,jagung,umum',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum/posts', 'public');
        }

        $post = ForumPost::create([
            'user_id'   => $request->user()->id,
            'title'     => $validated['title'],
            'content'   => $validated['content'],
            'commodity' => $validated['commodity'],
            'image'     => $imagePath,
            'status'    => 'pending', // selalu pending dulu, admin approve
        ]);

        return $this->successResponse(
            new ForumPostResource($post->load('user')),
            'Pertanyaan berhasil dikirim dan menunggu moderasi.',
            201
        );
    }

    // ============================================================
    // REPLY — Semua user bisa membalas post yang approved
    // POST /api/forum/{id}/replies
    // ============================================================
    public function reply(Request $request, string $id)
    {
        $post = ForumPost::approved()->findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|min:10',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum/replies', 'public');
        }

        $isExpert = $request->user()->isExpert();

        $reply = ForumReply::create([
            'post_id'          => $post->id,
            'user_id'          => $request->user()->id,
            'content'          => $validated['content'],
            'image'            => $imagePath,
            'is_expert_answer' => $isExpert,
        ]);

        // Jika yang menjawab adalah pakar/admin, tandai post sebagai answered
        if ($isExpert) {
            $post->update(['is_answered' => true]);
        }

        return $this->successResponse(
            new ForumReplyResource($reply->load('user')),
            'Balasan berhasil dikirim.',
            201
        );
    }

    // ============================================================
    // UPVOTE — Petani bisa upvote reply yang membantu
    // POST /api/forum/replies/{replyId}/upvote
    // ============================================================
    public function upvote(Request $request, string $replyId)
    {
        $reply = ForumReply::findOrFail($replyId);

        // Increment upvote
        $reply->increment('upvotes');

        return $this->successResponse([
            'upvotes' => $reply->fresh()->upvotes,
        ], 'Upvote berhasil.');
    }

    // ============================================================
    // MY POSTS — Post milik user yang sedang login
    // GET /api/forum/my-posts
    // ============================================================
    public function myPosts(Request $request)
    {
        $posts = ForumPost::where('user_id', $request->user()->id)
                          ->with(['user', 'expertReply.user'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        return $this->paginatedResponse(
            $posts->through(fn($post) => new ForumPostResource($post))
        );
    }

    // ============================================================
    // MODERATE — Hanya pakar/admin: approve atau reject post
    // PATCH /api/forum/{id}/moderate
    // Body: {"status": "approved"} atau {"status": "rejected"}
    // ============================================================
    public function moderate(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // Ambil post tanpa filter approved karena kita mau moderate yang pending
        $post = ForumPost::findOrFail($id);

        $post->update(['status' => $validated['status']]);

        return $this->successResponse(
            null,
            'Status post berhasil diubah menjadi ' . $validated['status'] . '.'
        );
    }

    // ============================================================
    // PENDING — Hanya pakar/admin: list post yang menunggu moderasi
    // GET /api/forum/pending
    // ============================================================
    public function pending(Request $request)
    {
        $posts = ForumPost::where('status', 'pending')
                          ->with('user')
                          ->orderBy('created_at', 'asc')
                          ->paginate(20);

        return $this->paginatedResponse(
            $posts->through(fn($post) => new ForumPostResource($post))
        );
    }
}
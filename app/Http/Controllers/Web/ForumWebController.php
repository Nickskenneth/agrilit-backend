<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumWebController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumPost::with('user')->orderBy('created_at', 'desc');

        $status = $request->get('status', 'all');
        if ($status === 'pending')  $query->where('status', 'pending');
        if ($status === 'approved') $query->where('status', 'approved');
        if ($status === 'answered') $query->where('is_answered', true);

        if ($request->filled('commodity')) {
            $query->where('commodity', $request->commodity);
        }

        $posts = $query->paginate(20)->withQueryString();
        $pendingCount = ForumPost::where('status', 'pending')->count();

        return view('admin.forum.index', compact('posts', 'pendingCount', 'status'));
    }

    public function show(string $id)
    {
        $post = ForumPost::with([
            'user',
            'replies' => fn($q) => $q->with('user')->orderBy('is_expert_answer', 'desc')->orderBy('created_at'),
        ])->findOrFail($id);

        return view('admin.forum.show', compact('post'));
    }

    public function moderate(Request $request, string $id)
    {
        $post = ForumPost::findOrFail($id);
        $request->validate(['status' => 'required|in:approved,rejected']);
        $post->update(['status' => $request->status]);

        $label = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Post forum berhasil {$label}.");
    }

    public function reply(Request $request, string $id)
    {
        $post = ForumPost::approved()->findOrFail($id);
        $request->validate(['content' => 'required|string|min:10']);

        ForumReply::create([
            'post_id'          => $post->id,
            'user_id'          => Auth::id(),
            'content'          => $request->content,
            'is_expert_answer' => Auth::user()->isExpert(),
        ]);

        if (Auth::user()->isExpert()) {
            $post->update(['is_answered' => true]);
        }

        return back()->with('success', 'Jawaban berhasil dikirim.');
    }
}

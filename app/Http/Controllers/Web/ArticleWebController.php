<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author')->orderBy('created_at', 'desc');

        if ($request->filled('commodity')) {
            $query->where('commodity', $request->commodity);
        }
        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->paginate(15)->withQueryString();
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.form', ['article' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'commodity' => 'required|in:cabai,kentang,jagung,umum',
            'category'  => 'required|in:budidaya,pemupukan,pengendalian,pascapanen,umum',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        }

        $isPublished = $request->has('is_published');

        Article::create([
            'author_id'    => Auth::id(),
            'title'        => $validated['title'],
            'slug'         => Str::slug($validated['title']) . '-' . uniqid(),
            'content'      => $validated['content'],
            'excerpt'      => Str::limit(strip_tags($validated['content']), 150),
            'thumbnail'    => $thumbnailPath,
            'commodity'    => $validated['commodity'],
            'category'     => $validated['category'],
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.form', compact('article'));
    }

    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'commodity' => 'required|in:cabai,kentang,jagung,umum',
            'category'  => 'required|in:budidaya,pemupukan,pengendalian,pascapanen,umum',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $thumbnailPath = $article->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath) Storage::disk('public')->delete($thumbnailPath);
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        }

        $isPublished = $request->has('is_published');

        $article->update([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'excerpt'      => Str::limit(strip_tags($validated['content']), 150),
            'thumbnail'    => $thumbnailPath,
            'commodity'    => $validated['commodity'],
            'category'     => $validated['category'],
            'is_published' => $isPublished,
            'published_at' => $isPublished && !$article->published_at ? now() : $article->published_at,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete(); // soft delete
        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    public function togglePublish(string $id)
    {
        $article = Article::findOrFail($id);
        $article->update([
            'is_published' => !$article->is_published,
            'published_at' => !$article->is_published ? now() : null,
        ]);
        $status = $article->is_published ? 'dipublikasikan' : 'disimpan sebagai draft';
        return back()->with('success', "Artikel berhasil {$status}.");
    }
}

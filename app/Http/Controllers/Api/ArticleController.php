<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    // ============================================================
    // INDEX — List semua artikel (dengan filter & pagination)
    // GET /api/articles
    // Query params:
    //   ?commodity=cabai          → filter komoditas
    //   ?category=pengendalian    → filter kategori
    //   ?search=antraknosa        → pencarian judul
    //   ?updated_after=2024-01-01 → untuk offline sync delta
    //   ?per_page=10              → jumlah per halaman
    // ============================================================
    public function index(Request $request)
    {
        $query = Article::published()
                        ->with('author')
                        ->orderBy('published_at', 'desc');

        // Filter komoditas
        if ($request->filled('commodity')) {
            $query->forCommodity($request->commodity);
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Pencarian judul & konten
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('excerpt', 'like', "%{$keyword}%");
            });
        }

        // Offline sync delta — hanya artikel yang diupdate setelah timestamp tertentu
        // Flutter mengirim timestamp terakhir sync, server kembalikan yang lebih baru saja
        if ($request->filled('updated_after')) {
            $query->updatedAfter($request->updated_after);
        }

        $perPage  = min($request->get('per_page', 10), 50); // max 50 per request
        $articles = $query->paginate($perPage);

        return $this->paginatedResponse($articles->through(
            fn($article) => new ArticleResource($article)
        ));
    }

    // ============================================================
    // SHOW — Detail satu artikel
    // GET /api/articles/{id}
    // ============================================================
    public function show(string $id)
    {
        $article = Article::published()
                          ->with('author')
                          ->findOrFail($id);

        // Increment view counter secara efisien tanpa trigger events/timestamps
        Article::where('id', $id)->increment('views');

        return $this->successResponse(
            new ArticleResource($article)
        );
    }

    // ============================================================
    // SYNC — Endpoint khusus untuk Flutter offline sync
    // GET /api/articles/sync
    // Mengembalikan daftar ID + updated_at saja (ringan)
    // Flutter bandingkan dengan SQLite lokal, fetch detail yang berbeda
    // ============================================================
    public function sync(Request $request)
    {
        $articles = Article::published()
                           ->select('id', 'updated_at')
                           ->orderBy('updated_at', 'desc')
                           ->get()
                           ->map(fn($a) => [
                               'id'         => $a->id,
                               'updated_at' => $a->updated_at->toISOString(),
                           ]);

        return $this->successResponse($articles, 'Sync metadata berhasil.');
    }
}
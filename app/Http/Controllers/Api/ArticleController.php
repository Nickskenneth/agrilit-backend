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
        $perPage   = min($request->get('per_page', 10), 50);
        $keyword   = $request->filled('search') ? trim($request->search) : null;
        $commodity = $request->filled('commodity') ? $request->commodity : null;
        $category  = $request->filled('category')  ? $request->category  : null;

        // ============================================================
        // SEARCH PATH — gunakan Meilisearch via Laravel Scout
        // Aktif hanya saat ada keyword pencarian.
        // Meilisearch memberikan: typo tolerance, partial match,
        // dan pencarian ke dalam isi artikel (content_plain).
        // ============================================================
        if ($keyword) {
            $scoutQuery = Article::search($keyword)
                ->query(fn ($q) => $q->with('author')->published());

            // Filter komoditas & kategori via Meilisearch filterable attributes
            if ($commodity) $scoutQuery->where('commodity', $commodity);
            if ($category)  $scoutQuery->where('category',  $category);

            $articles = $scoutQuery->paginate($perPage);

            return $this->paginatedResponse($articles->through(
                fn($article) => new ArticleResource($article)
            ));
        }

        // ============================================================
        // BROWSE PATH — tidak ada keyword, gunakan Eloquent biasa
        // Lebih efisien untuk listing & filtering tanpa pencarian teks.
        // ============================================================
        $query = Article::published()
                        ->with('author')
                        ->orderBy('published_at', 'desc');

        if ($commodity) $query->forCommodity($commodity);
        if ($category)  $query->where('category', $category);

        // Offline sync delta — hanya artikel yang diupdate setelah timestamp tertentu
        if ($request->filled('updated_after')) {
            $query->updatedAfter($request->updated_after);
        }

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
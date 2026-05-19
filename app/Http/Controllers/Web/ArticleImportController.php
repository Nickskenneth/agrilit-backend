<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\DocxImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleImportController extends Controller
{
    public function __construct(
        private DocxImportService $importService
    ) {}

    // ============================================================
    // SHOW FORM — Halaman upload
    // GET /admin/articles/import
    // ============================================================
    public function showForm()
    {
        return view('admin.articles.import');
    }

    // ============================================================
    // PREVIEW — Parse file & tampilkan preview sebelum simpan
    // POST /admin/articles/import/preview
    // ============================================================
    public function preview(Request $request)
    {
        $request->validate([
            'docx_file' => 'required|file|mimes:docx,doc|max:10240', // max 10MB
        ]);

        // Simpan file sementara
        $file     = $request->file('docx_file');
        $tempPath = $file->store('imports/temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        // Parse dokumen
        $parsed = $this->importService->parse($fullPath);

        // Hapus file temp
        \Storage::disk('local')->delete($tempPath);

        // Jika ada error fatal, kembali ke form
        if (!empty($parsed['errors'])) {
            return back()
                ->withErrors(['docx_file' => implode(' ', $parsed['errors'])])
                ->withInput();
        }

        // Simpan hasil parse di session untuk digunakan saat konfirmasi
        session(['import_preview' => $parsed]);

        return view('admin.articles.import_preview', [
            'parsed' => $parsed,
        ]);
    }

    // ============================================================
    // CONFIRM — Simpan artikel dari session preview
    // POST /admin/articles/import/confirm
    // ============================================================
    public function confirm(Request $request)
    {
        $parsed = session('import_preview');

        if (!$parsed) {
            return redirect()->route('admin.articles.import')
                ->with('error', 'Sesi preview sudah berakhir. Upload ulang file.');
        }

        // Admin bisa override beberapa field dari form konfirmasi
        $title     = $request->input('title',     $parsed['title']);
        $commodity = $request->input('commodity', $parsed['commodity']);
        $category  = $request->input('category',  $parsed['category']);
        $publish   = $request->has('is_published');

        Article::create([
            'author_id'    => Auth::id(),
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . uniqid(),
            'content'      => $parsed['content'],
            'excerpt'      => $parsed['excerpt'],
            'commodity'    => $commodity,
            'category'     => $category,
            'is_published' => $publish,
            'published_at' => $publish ? now() : null,
        ]);

        // Hapus session preview
        session()->forget('import_preview');

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$title}\" berhasil diimport.");
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ArticleWebController;
use App\Http\Controllers\Web\SopWebController;
use App\Http\Controllers\Web\ForumWebController;
use App\Http\Controllers\Web\DiseaseScanWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\ArticleImportController;


// ============================================================
// GUEST — Halaman login (redirect ke dashboard jika sudah login)
// ============================================================
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login',  [DashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [DashboardController::class, 'login'])->name('login.post');
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

// ============================================================
// PROTECTED — Harus login, role pakar atau admin
// ============================================================
Route::middleware(['auth.web', 'role.web:pakar,admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Articles
        Route::get('/articles',           [ArticleWebController::class, 'index'])->name('articles.index');
        Route::get('/articles/create',    [ArticleWebController::class, 'create'])->name('articles.create');
        Route::post('/articles',          [ArticleWebController::class, 'store'])->name('articles.store');
        Route::get('/articles/{id}/edit', [ArticleWebController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{id}',      [ArticleWebController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{id}',   [ArticleWebController::class, 'destroy'])->name('articles.destroy');
        Route::patch('/articles/{id}/toggle-publish', [ArticleWebController::class, 'togglePublish'])->name('articles.toggle');

        // SOPs
        Route::get('/sops',           [SopWebController::class, 'index'])->name('sops.index');
        Route::get('/sops/create',    [SopWebController::class, 'create'])->name('sops.create');
        Route::post('/sops',          [SopWebController::class, 'store'])->name('sops.store');
        Route::get('/sops/{id}/edit', [SopWebController::class, 'edit'])->name('sops.edit');
        Route::put('/sops/{id}',      [SopWebController::class, 'update'])->name('sops.update');
        Route::delete('/sops/{id}',   [SopWebController::class, 'destroy'])->name('sops.destroy');

        // Forum moderation
        Route::get('/forum',                        [ForumWebController::class, 'index'])->name('forum.index');
        Route::get('/forum/{id}',                   [ForumWebController::class, 'show'])->name('forum.show');
        Route::patch('/forum/{id}/moderate',        [ForumWebController::class, 'moderate'])->name('forum.moderate');
        Route::post('/forum/{id}/reply',            [ForumWebController::class, 'reply'])->name('forum.reply');

        // Disease Map
        Route::get('/disease-map', [DiseaseScanWebController::class, 'index'])->name('disease-map.index');

        // Endpoint data peta untuk CMS (gunakan session auth, bukan token)
        Route::get('/map-data', function () {
            $scans = \App\Models\DiseaseScan::withLocation()
                ->select([
                    'id',
                    'commodity',
                    'result_label_id',
                    'confidence_score',
                    'latitude',
                    'longitude',
                    'location_name',
                    'scanned_at'
                ])
                ->when(request('commodity'), fn($q) => $q->forCommodity(request('commodity')))
                ->when(request('disease'), fn($q) => $q->where('result_label_id', 'like', '%' . request('disease') . '%'))
                ->latest('scanned_at')
                ->limit(500)
                ->get()
                ->map(fn($s) => [
                    'id'              => $s->id,
                    'commodity'       => $s->commodity,
                    'result_label_id' => $s->result_label_id,
                    'confidence'      => $s->confidence_score
                        ? round($s->confidence_score * 100, 1) . '%'
                        : 'N/A',
                    'lat'             => (float) $s->latitude,
                    'lng'             => (float) $s->longitude,
                    'location_name'   => $s->location_name,
                    'scanned_at'      => $s->scanned_at,
                ]);

            return response()->json(['data' => $scans]);
        })->name('map-data');

        // Users — hanya admin
        Route::middleware('role.web:admin')->group(function () {
            Route::get('/users',           [UserWebController::class, 'index'])->name('users.index');
            Route::get('/users/create',    [UserWebController::class, 'create'])->name('users.create');
            Route::post('/users',          [UserWebController::class, 'store'])->name('users.store');
            Route::patch('/users/{id}/role', [UserWebController::class, 'updateRole'])->name('users.role');
            Route::delete('/users/{id}',   [UserWebController::class, 'destroy'])->name('users.destroy');
            Route::get('/articles/import',         [ArticleImportController::class, 'showForm'])->name('articles.import');
            Route::post('/articles/import/preview', [ArticleImportController::class, 'preview'])->name('articles.import.preview');
            Route::post('/articles/import/confirm', [ArticleImportController::class, 'confirm'])->name('articles.import.confirm');
        });
    });

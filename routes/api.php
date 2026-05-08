<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\SopController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\DiseaseScanController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// AUTHENTICATION (Public & Protected)
// ============================================================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',       [AuthController::class, 'me']);
    });
});

// ============================================================
// PROTECTED ROUTES (Must be Logged In)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // ------------------------------------------------------------
    // 1. KHUSUS PAKAR & ADMIN
    // (Diletakkan di atas agar route statis seperti /pending 
    // tidak dianggap sebagai {id} oleh route dinamis di bawahnya)
    // ------------------------------------------------------------
    Route::middleware('role:pakar,admin')->group(function () {
        Route::get('/forum/pending',          [ForumController::class, 'pending']);
        Route::patch('/forum/{id}/moderate',  [ForumController::class, 'moderate']);

        // Peta sebaran penyakit
        Route::get('/scans/map', [DiseaseScanController::class, 'mapData']);
    });

    // ------------------------------------------------------------
    // 2. SEMUA ROLE (Petani, Pakar, Admin)
    // ------------------------------------------------------------
    Route::middleware('role:petani,pakar,admin')->group(function () {

        // --- Articles ---
        Route::get('/articles/sync', [ArticleController::class, 'sync']);
        Route::get('/articles',      [ArticleController::class, 'index']);
        Route::get('/articles/{id}', [ArticleController::class, 'show']);

        // --- SOP ---
        Route::get('/sops/sync', [SopController::class, 'sync']);
        Route::get('/sops',      [SopController::class, 'index']);
        Route::get('/sops/{id}', [SopController::class, 'show']);

        // --- Forum ---
        Route::get('/forum/my-posts',                    [ForumController::class, 'myPosts']);
        Route::get('/forum',                             [ForumController::class, 'index']);

        // Route dinamis {id} diletakkan setelah route statis (my-posts)
        Route::get('/forum/{id}',                        [ForumController::class, 'show']);

        Route::post('/forum',                            [ForumController::class, 'store']);
        Route::post('/forum/{id}/replies',               [ForumController::class, 'reply']);
        Route::post('/forum/replies/{replyId}/upvote',   [ForumController::class, 'upvote']);

        // --- Disease Scans ---
        // Statis sebelum dinamis
        // --- Disease Scans ---
        Route::get('/scans',        [DiseaseScanController::class, 'history']);
        Route::get('/scans/{id}',   [DiseaseScanController::class, 'show']);
        Route::post('/scans',       [DiseaseScanController::class, 'scan']);
        Route::post('/scans/sync',  [DiseaseScanController::class, 'syncOffline']);
    });

    // ------------------------------------------------------------
    // 3. KHUSUS ADMIN ONLY
    // ------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        // Tambahkan route khusus admin di sini nanti
    });
});

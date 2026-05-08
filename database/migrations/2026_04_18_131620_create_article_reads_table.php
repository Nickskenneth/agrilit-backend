<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini mencatat artikel mana yang sudah di-cache oleh user mana
        // Berguna untuk sync delta — hanya kirim artikel BARU/UPDATE saja
        Schema::create('article_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');

            // Versi terakhir yang di-cache user ini
            $table->timestamp('cached_at')->nullable();

            // Sudah dibaca atau belum
            $table->boolean('is_read')->default(false);

            $table->timestamps();

            // Satu user hanya punya satu record per artikel
            $table->unique(['user_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_reads');
    }
};
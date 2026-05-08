<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            // Penulis artikel (pakar/admin)
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('title');

            // Slug untuk URL yang bersih, misal: "cara-tanam-cabai-2024"
            $table->string('slug')->unique();

            // Konten artikel dalam format HTML atau Markdown
            $table->longText('content');

            // Ringkasan singkat untuk ditampilkan di list artikel
            $table->text('excerpt')->nullable();

            // Gambar sampul artikel
            $table->string('thumbnail')->nullable();

            // Komoditas yang relevan
            $table->enum('commodity', ['cabai', 'kentang', 'jagung', 'umum'])
                  ->default('umum');

            // Kategori konten artikel
            $table->enum('category', [
                'budidaya',      // cara tanam
                'pemupukan',     // nutrisi tanaman
                'pengendalian',  // OPT (hama & penyakit)
                'pascapanen',    // setelah panen
                'umum'
            ])->default('umum');

            // Status publikasi — draft tidak muncul di mobile
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // Jumlah dibaca (untuk sorting popularitas)
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
            $table->softDeletes(); // data tidak langsung terhapus permanen
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
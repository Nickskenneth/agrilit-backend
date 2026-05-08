<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('title');
            $table->text('content');

            // Tag komoditas agar forum bisa difilter
            $table->enum('commodity', ['cabai', 'kentang', 'jagung', 'umum'])
                  ->default('umum');

            // Gambar pendukung pertanyaan (opsional)
            $table->string('image')->nullable();

            // Status moderasi — postingan baru masuk sebagai 'pending'
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            // Apakah sudah ada jawaban pakar
            $table->boolean('is_answered')->default(false);

            // Counter view
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
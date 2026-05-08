<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();

            // Balasan ini merupakan balasan dari post mana
            $table->foreignId('post_id')
                  ->constrained('forum_posts')
                  ->onDelete('cascade');

            // Siapa yang membalas
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('content');

            // Gambar pendukung jawaban (opsional)
            $table->string('image')->nullable();

            // Flag apakah ini jawaban resmi dari pakar
            // Hanya satu reply per post yang bisa jadi 'expert_answer'
            $table->boolean('is_expert_answer')->default(false);

            // Jumlah upvote dari sesama petani
            $table->unsignedInteger('upvotes')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
    }
};
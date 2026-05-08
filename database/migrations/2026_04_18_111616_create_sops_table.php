<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sops', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->unique();

            // Komoditas spesifik SOP ini
            $table->enum('commodity', ['cabai', 'kentang', 'jagung']);

            // Deskripsi singkat SOP
            $table->text('description')->nullable();

            // Durasi total masa tanam (dalam hari)
            $table->unsignedInteger('duration_days')->nullable();

            // Data per-bulan dalam format JSON
            // Struktur: [{"month": 1, "week": 1, "activity": "Persiapan lahan", "notes": "..."}]
            $table->json('monthly_calendar');

            // Kebutuhan pupuk & pestisida dalam format JSON
            // Struktur: [{"name": "Urea", "dose": "200kg/ha", "timing": "minggu ke-2"}]
            $table->json('inputs_needed')->nullable();

            $table->string('thumbnail')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sops');
    }
};
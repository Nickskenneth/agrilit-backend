<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_scans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Path gambar yang diupload (setelah dikompresi dari Flutter)
            $table->string('image_path');

            $table->enum('commodity', ['cabai', 'kentang', 'jagung']);

            // Label hasil prediksi AI, contoh: "Bacterial_spot"
            // Nullable karena bisa jadi belum di-sync saat offline
            $table->string('result_label')->nullable();

            // Label yang sudah diformat ramah pengguna, contoh: "Bercak Bakteri"
            $table->string('result_label_id')->nullable();

            // Skor kepercayaan model (0.00 - 1.00)
            $table->decimal('confidence_score', 5, 4)->nullable();

            // ===== GEOTAGGING =====
            // Koordinat diambil saat foto, bisa offline
            // decimal(10,8) -> presisi ~1.1 meter, cukup untuk peta
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Nama lokasi hasil reverse geocoding (opsional, dilakukan saat online)
            $table->string('location_name')->nullable();

            // ===== OFFLINE SYNC FLAG =====
            // false = data masih di SQLite Flutter, belum sampai server
            // true  = data sudah berhasil dikirim & tersimpan di server
            $table->boolean('synced')->default(true);

            // Waktu foto diambil (bisa berbeda dengan created_at jika offline)
            $table->timestamp('scanned_at');

            // Catatan tambahan dari petani (opsional)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_scans');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role: 'petani', 'pakar', 'admin'
            $table->enum('role', ['petani', 'pakar', 'admin'])
                  ->default('petani')
                  ->after('email');

            // Nomor HP untuk kontak
            $table->string('phone', 20)->nullable()->after('role');

            // Wilayah/lokasi domisili petani
            $table->string('region')->nullable()->after('phone');

            // Foto profil (opsional)
            $table->string('avatar')->nullable()->after('region');

            // Untuk Sanctum: hapus token saat logout
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'region', 'avatar', 'last_login_at'
            ]);
        });
    }
};
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // URUTAN INI WAJIB DIIKUTI karena ada dependency foreign key
        $this->call([
            UserSeeder::class,
            ArticleSeeder::class,
            SopSeeder::class,
            ForumSeeder::class,
            DiseaseScanSeeder::class,
        ]);
    }
}
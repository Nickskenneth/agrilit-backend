<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Meilisearch\Client;

class DiagnoseSearch extends Command
{
    protected $signature   = 'search:diagnose';
    protected $description = 'Diagnosa dan perbaiki konfigurasi Scout + Meilisearch';

    public function handle(): int
    {
        $this->info('=== DIAGNOSA SCOUT + MEILISEARCH ===');
        $this->newLine();

        // 1. Cek SCOUT_DRIVER
        $driver = config('scout.driver');
        $this->line("1. Scout driver  : <comment>{$driver}</comment>");

        if ($driver !== 'meilisearch') {
            $this->error('   ✗ SCOUT_DRIVER bukan meilisearch!');
            $this->warn('   Fix: tambahkan SCOUT_DRIVER=meilisearch di .env lalu jalankan:');
            $this->warn('        php artisan config:clear && php artisan config:cache');
        } else {
            $this->info('   ✓ Driver meilisearch OK');
        }

        // 2. Cek Meilisearch host & key
        $host = config('scout.meilisearch.host');
        $key  = config('scout.meilisearch.key');
        $this->line("2. Meilisearch host : <comment>{$host}</comment>");
        $this->line("   Meilisearch key  : <comment>" . ($key ? substr($key, 0, 8) . '...' : 'TIDAK ADA') . "</comment>");

        // 3. Koneksi ke Meilisearch
        $this->line('3. Test koneksi Meilisearch...');
        try {
            $client = new Client($host, $key);
            $health = $client->health();
            $this->info("   ✓ Meilisearch OK — status: {$health['status']}");
        } catch (\Exception $e) {
            $this->error("   ✗ Gagal koneksi: " . $e->getMessage());
            return self::FAILURE;
        }

        // 4. Cek index articles
        $this->line('4. Cek index articles...');
        try {
            $index = $client->index('articles');
            $stats = $index->stats();
            $count = $stats['numberOfDocuments'];
            $this->info("   ✓ Index articles: <comment>{$count} dokumen</comment>");

            if ($count === 0) {
                $this->warn('   ✗ Index kosong! Jalankan: php artisan scout:import "App\Models\Article"');
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Gagal akses index: " . $e->getMessage());
        }

        // 5. Cek jumlah artikel published di DB
        $dbCount = Article::published()->count();
        $this->line("5. Artikel published di DB: <comment>{$dbCount}</comment>");

        // 6. Direct Meilisearch search
        $this->line('6. Test search Meilisearch langsung...');
        try {
            $index  = $client->index('articles');
            $result = $index->search('cabai');
            $hits   = count($result->getHits());
            $this->info("   ✓ Search 'cabai' langsung di Meilisearch: <comment>{$hits} hasil</comment>");

            if ($hits > 0) {
                $firstHit = $result->getHits()[0];
                $this->line("   Sample hit: ID={$firstHit['id']}, title={$firstHit['title']}");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Gagal search: " . $e->getMessage());
        }

        // 7. Scout search via Laravel (ini yang biasanya bermasalah)
        $this->line('7. Test Scout search (Laravel)...');
        try {
            $scoutResults = Article::search('cabai')->get();
            $scoutCount   = $scoutResults->count();
            $this->info("   ✓ Scout search 'cabai': <comment>{$scoutCount} hasil</comment>");

            if ($scoutCount === 0) {
                $this->error("   ✗ Scout mengembalikan 0 hasil. Mungkin masalah mapping ID atau query callback.");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Scout error: " . $e->getMessage());
        }

        // 8. Scout search + query callback (seperti di controller)
        $this->line('8. Test Scout search + query callback...');
        try {
            $results = Article::search('cabai')
                ->query(fn ($q) => $q->with('author')->published())
                ->get();
            $count = $results->count();
            $this->info("   ✓ Scout search + callback 'cabai': <comment>{$count} hasil</comment>");

            if ($count === 0 && isset($scoutCount) && $scoutCount > 0) {
                $this->error("   ✗ Query callback menyebabkan masalah!");
                $this->warn("   Cek apakah 'published()' scope memfilter hasil yang salah.");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Error: " . $e->getMessage());
        }

        // 9. Verifikasi ID match antara Meilisearch dan DB
        $this->line('9. Verifikasi ID di Meilisearch vs DB...');
        try {
            $meiliHits = $client->index('articles')->search('cabai')->getHits();
            $meiliIds  = collect($meiliHits)->pluck('id');
            $this->line("   ID dari Meilisearch: " . $meiliIds->implode(', '));

            $dbArticles = Article::whereIn('id', $meiliIds)->get();
            $this->line("   ID ada di DB: " . $dbArticles->pluck('id')->implode(', '));

            $missing = $meiliIds->diff($dbArticles->pluck('id'));
            if ($missing->isNotEmpty()) {
                $this->error("   ✗ ID ada di Meilisearch tapi TIDAK ada di DB: " . $missing->implode(', '));
            } else {
                $this->info("   ✓ Semua ID Meilisearch ada di DB");
            }

            $unpublished = $dbArticles->where('is_published', false);
            if ($unpublished->isNotEmpty()) {
                $this->error("   ✗ Artikel di index tapi is_published=false: " . $unpublished->pluck('id')->implode(', '));
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Error verifikasi: " . $e->getMessage());
        }

        $this->newLine();
        $this->line('=== SELESAI ===');
        $this->line('Jika ada masalah, jalankan:');
        $this->line('  php artisan config:clear');
        $this->line('  php artisan config:cache');
        $this->line('  php artisan scout:import "App\Models\Article"');

        return self::SUCCESS;
    }
}

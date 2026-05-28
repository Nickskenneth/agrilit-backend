<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;

class SetupMeilisearchIndex extends Command
{
    protected $signature   = 'meilisearch:setup';
    protected $description = 'Konfigurasi Meilisearch index settings untuk articles (filterable, searchable, ranking)';

    public function handle(): int
    {
        $this->info('Mengonfigurasi Meilisearch index untuk articles...');

        $client = new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );

        $index = $client->index('articles');

        // ============================================================
        // 1. SEARCHABLE ATTRIBUTES — field yang dicari, urutan = prioritas
        //    title paling atas → match di judul lebih relevan dari konten
        // ============================================================
        $index->updateSearchableAttributes([
            'title',
            'excerpt',
            'content_plain',
            'category',
            'commodity',
        ]);
        $this->line('  v Searchable attributes dikonfigurasi');

        // ============================================================
        // 2. FILTERABLE ATTRIBUTES — field yang bisa dipakai untuk filter
        // ============================================================
        $index->updateFilterableAttributes([
            'commodity',
            'category',
            'is_published',
        ]);
        $this->line('  v Filterable attributes dikonfigurasi');

        // ============================================================
        // 3. RANKING RULES — urutan relevansi hasil pencarian
        // ============================================================
        $index->updateRankingRules([
            'words',
            'typo',
            'proximity',
            'attribute',
            'sort',
            'exactness',
        ]);
        $this->line('  v Ranking rules dikonfigurasi');

        // ============================================================
        // 4. TYPO TOLERANCE
        // ============================================================
        $index->updateTypoTolerance([
            'enabled'             => true,
            'minWordSizeForTypos' => [
                'oneTypo'  => 4,
                'twoTypos' => 8,
            ],
        ]);
        $this->line('  v Typo tolerance dikonfigurasi');

        $this->newLine();
        $this->info('Setup selesai! Jalankan berikutnya:');
        $this->line('  php artisan scout:import "App\Models\Article"');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

class GenerateArticleTemplate extends Command
{
    protected $signature   = 'agrilit:make-template';
    protected $description = 'Generate template DOCX untuk import artikel';

    public function handle(): void
    {
        $phpWord = new PhpWord();
        $phpWord->addTitleStyle(1, [
            'bold' => true,
            'size' => 18,
            'name' => 'Arial'
        ]);
        $phpWord->addTitleStyle(2, [
            'bold' => true,
            'size' => 14,
            'name' => 'Arial',
            'color' => '1D6A3A'
        ]);

        $section = $phpWord->addSection();

        // ===== INSTRUKSI =====
        $section->addText(
            '📌 PANDUAN PENGISIAN TEMPLATE AgriLit',
            ['bold' => true, 'size' => 12, 'color' => '1D6A3A']
        );
        $section->addText(
            'Aturan wajib: (1) Judul pakai Heading 1. '
                . '(2) Bagian META wajib ada dengan Heading 2. '
                . '(3) Bagian konten pakai Heading 2, nama bebas sesuai topik. '
                . '(4) Isi konten pakai style Normal.',
            ['size' => 10, 'color' => '666666', 'italic' => true]
        );
        $section->addText(
            'Hapus baris instruksi ini sebelum upload.',
            ['size' => 10, 'color' => 'CC0000', 'italic' => true, 'bold' => true]
        );
        $section->addTextBreak(1);

        // ===== JUDUL =====
        $section->addTitle('Tulis Judul Artikel Di Sini', 1);
        $section->addTextBreak(1);

        // ===== META (wajib) =====
        $section->addTitle('META', 2);
        $section->addText(
            'Komoditas: cabai',
            ['size' => 11],
            ['spaceAfter' => 0]
        );
        $section->addText(
            'Kategori: budidaya',
            ['size' => 11],
            ['spaceAfter' => 60]
        );
        $section->addText(
            '[ Komoditas: cabai / kentang / jagung / umum ]',
            ['size' => 9, 'color' => '999999', 'italic' => true],
            ['spaceAfter' => 0]
        );
        $section->addText(
            '[ Kategori: budidaya / pemupukan / pengendalian / pascapanen / umum ]',
            ['size' => 9, 'color' => '999999', 'italic' => true],
            ['spaceAfter' => 200]
        );

        // ===== CONTOH BAGIAN 1 =====
        $section->addTitle('BAGIAN PERTAMA (ganti nama sesuai topik)', 2);
        $section->addText(
            'Tulis isi konten bagian ini. Nama bagian bebas — '
                . 'contoh: PENDAHULUAN, PERSIAPAN LAHAN, WAKTU PANEN, dsb.',
            ['size' => 11],
            ['spaceAfter' => 200]
        );

        // ===== CONTOH BAGIAN 2 (dengan bullet) =====
        $section->addTitle('BAGIAN KEDUA (contoh dengan poin-poin)', 2);
        $listStyle = [
            'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED
        ];
        $section->addListItem(
            'Poin pertama — tulis langkah atau fakta',
            0,
            ['size' => 11],
            $listStyle
        );
        $section->addListItem(
            'Poin kedua',
            0,
            ['size' => 11],
            $listStyle
        );
        $section->addListItem(
            'Poin ketiga',
            0,
            ['size' => 11],
            $listStyle
        );
        $section->addTextBreak(1);

        // ===== CONTOH BAGIAN 3 =====
        $section->addTitle('BAGIAN KETIGA (tambah sebanyak yang diperlukan)', 2);
        $section->addText(
            'Tidak ada batas jumlah bagian. '
                . 'Setiap Heading 2 akan menjadi sub-judul di artikel.',
            ['size' => 11],
            ['spaceAfter' => 200]
        );

        // ===== KESIMPULAN (opsional tapi disarankan) =====
        $section->addTitle('KESIMPULAN', 2);
        $section->addText(
            'Tulis ringkasan atau penutup artikel. Bagian ini opsional '
                . 'tapi sangat disarankan.',
            ['size' => 11]
        );

        $path   = public_path('templates/AgriLit_Template_Artikel.docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        $this->info('✅ Template berhasil dibuat: ' . $path);
    }
}

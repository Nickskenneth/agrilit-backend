<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\Title;

class DocxImportService
{
    // Nilai heading style yang dianggap sebagai "Heading 1" dan "Heading 2"
    private const H1_STYLES = ['Heading1', 'Heading 1', 'heading1'];
    private const H2_STYLES = ['Heading2', 'Heading 2', 'heading2'];

    // Nilai komoditas & kategori yang valid
    private const VALID_COMMODITIES = ['cabai', 'kentang', 'jagung', 'umum'];
    private const VALID_CATEGORIES  = [
        'budidaya',
        'pemupukan',
        'pengendalian',
        'pascapanen',
        'umum'
    ];

    // ============================================================
    // PARSE — Entry point utama
    // Mengembalikan array siap disimpan ke database
    // atau array 'errors' jika ada masalah
    // ============================================================
    public function parse(string $filePath): array
    {
        try {
            $phpWord  = IOFactory::load($filePath);
            $sections = $phpWord->getSections();

            if (empty($sections)) {
                return ['errors' => ['Dokumen kosong atau tidak bisa dibaca.']];
            }

            // Kumpulkan semua elemen dari semua section
            $elements = [];
            foreach ($sections as $section) {
                foreach ($section->getElements() as $el) {
                    $elements[] = $el;
                }
            }

            return $this->extractContent($elements);
        } catch (\Throwable $e) {
            return ['errors' => ['Gagal membaca file: ' . $e->getMessage()]];
        }
    }

    // ============================================================
    // EXTRACT — Proses semua elemen menjadi struktur artikel
    // ============================================================
    private function extractContent(array $elements): array
    {
        $result = [
            'title'     => '',
            'commodity' => 'umum',
            'category'  => 'umum',
            'sections'  => [], // [['heading' => '...', 'content' => '...']]
            'errors'    => [],
            'warnings'  => [],
        ];

        $currentHeading = null;
        $currentContent = [];

        foreach ($elements as $element) {
            $styleInfo = $this->getElementStyle($element);
            $text      = $this->extractText($element);

            if (empty(trim($text))) continue;

            // ===== HEADING 1 = Judul Artikel =====
            if ($styleInfo === 'h1') {
                $result['title'] = trim($text);
                continue;
            }

            // ===== HEADING 2 = Nama Bagian =====
            if ($styleInfo === 'h2') {
                // Simpan bagian sebelumnya
                if ($currentHeading !== null) {
                    $this->flushSection(
                        $result,
                        $currentHeading,
                        $currentContent
                    );
                    $currentContent = [];
                }
                $currentHeading = strtoupper(trim($text));
                continue;
            }

            // ===== KONTEN NORMAL =====
            if ($currentHeading !== null) {
                // Bagian META — parse key:value
                if ($currentHeading === 'META') {
                    $this->parseMeta($result, $text);
                } else {
                    // Elemen list → tambah bullet
                    if ($element instanceof ListItem) {
                        $currentContent[] = '• ' . trim($text);
                    } else {
                        $currentContent[] = trim($text);
                    }
                }
            }
        }

        // Flush bagian terakhir
        if ($currentHeading !== null) {
            $this->flushSection($result, $currentHeading, $currentContent);
        }

        // ===== VALIDASI =====
        $this->validate($result);

        // ===== BUILD HTML CONTENT =====
        if (empty($result['errors'])) {
            $result['content'] = $this->buildHtml($result['sections']);
            $result['excerpt'] = $this->buildExcerpt($result['sections']);
        }

        return $result;
    }

    // ============================================================
    // PARSE META — Baca baris "Komoditas: cabai", "Kategori: ..."
    // ============================================================
    private function parseMeta(array &$result, string $text): void
    {
        if (stripos($text, 'komoditas:') === 0) {
            $value = strtolower(trim(substr($text, strlen('komoditas:'))));
            if (in_array($value, self::VALID_COMMODITIES)) {
                $result['commodity'] = $value;
            } else {
                $result['warnings'][] =
                    "Komoditas '{$value}' tidak dikenal, diset ke 'umum'.";
            }
        } elseif (stripos($text, 'kategori:') === 0) {
            $value = strtolower(trim(substr($text, strlen('kategori:'))));
            if (in_array($value, self::VALID_CATEGORIES)) {
                $result['category'] = $value;
            } else {
                $result['warnings'][] =
                    "Kategori '{$value}' tidak dikenal, diset ke 'umum'.";
            }
        }
    }

    // ============================================================
    // FLUSH SECTION — Simpan bagian konten ke array sections
    // ============================================================
    private function flushSection(
        array &$result,
        string $heading,
        array $lines
    ): void {
        // Skip bagian META dari konten artikel
        if ($heading === 'META') return;

        if (!empty($lines)) {
            $result['sections'][] = [
                'heading' => ucwords(strtolower($heading)),
                'content' => implode("\n", array_filter($lines)),
            ];
        }
    }

    // ============================================================
    // BUILD HTML — Konversi sections ke HTML untuk disimpan di DB
    // ============================================================
    private function buildHtml(array $sections): string
    {
        $html = '';
        foreach ($sections as $section) {
            $html .= '<h2>' . htmlspecialchars($section['heading']) . '</h2>';
            $lines = explode("\n", $section['content']);
            $inList = false;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (str_starts_with($line, '• ') || str_starts_with($line, '- ')) {
                    if (!$inList) {
                        $html   .= '<ul>';
                        $inList  = true;
                    }
                    $item  = ltrim($line, '• -');
                    $html .= '<li>' . htmlspecialchars(trim($item)) . '</li>';
                } else {
                    if ($inList) {
                        $html  .= '</ul>';
                        $inList = false;
                    }
                    $html .= '<p>' . htmlspecialchars($line) . '</p>';
                }
            }

            if ($inList) $html .= '</ul>';
        }
        return $html;
    }

    // ============================================================
    // BUILD EXCERPT — Ambil 150 karakter pertama dari konten
    // ============================================================
    private function buildExcerpt(array $sections): string
    {
        $plain = '';
        foreach ($sections as $section) {
            $plain .= ' ' . $section['content'];
        }
        return \Illuminate\Support\Str::limit(trim(strip_tags($plain)), 150);
    }

    // ============================================================
    // VALIDATE — Cek kelengkapan data wajib
    // ============================================================
    private function validate(array &$result): void
    {
        if (empty($result['title'])) {
            $result['errors'][] =
                'Judul artikel tidak ditemukan. '
                . 'Pastikan ada teks dengan style "Heading 1" di dokumen.';
        }

        if (empty($result['sections'])) {
            $result['errors'][] =
                'Konten artikel tidak ditemukan. '
                . 'Pastikan ada bagian dengan style "Heading 2" di dokumen.';
        }

        if (strlen($result['title']) > 255) {
            $result['errors'][] =
                'Judul terlalu panjang (maks 255 karakter).';
        }
    }

    // ============================================================
    // HELPER — Deteksi apakah elemen adalah H1 atau H2
    // ============================================================
    private function getElementStyle($element): ?string
    {
        // Cek via Title element (cara paling reliable di PhpWord)
        if ($element instanceof Title) {
            $depth = $element->getDepth();
            if ($depth === 1) return 'h1';
            if ($depth === 2) return 'h2';
            return null;
        }

        // Fallback: cek via paragraph style name
        if (method_exists($element, 'getParagraphStyle')) {
            $style = $element->getParagraphStyle();
            if ($style) {
                $styleName = is_string($style)
                    ? $style
                    : (method_exists($style, 'getStyleName')
                        ? $style->getStyleName()
                        : '');

                if (in_array($styleName, self::H1_STYLES)) return 'h1';
                if (in_array($styleName, self::H2_STYLES)) return 'h2';
            }
        }

        return null; // Normal text
    }

    // ============================================================
    // HELPER — Ekstrak semua teks dari elemen apapun
    // ============================================================
    private function extractText($element): string
    {
        // Title element
        if ($element instanceof Title) {
            $titleEl = $element->getText();
            if ($titleEl instanceof TextRun) {
                return $this->extractFromTextRun($titleEl);
            }
            if (is_string($titleEl)) return $titleEl;
            return '';
        }

        // TextRun (paragraf dengan mixed formatting)
        if ($element instanceof TextRun) {
            return $this->extractFromTextRun($element);
        }

        // Text
        if ($element instanceof Text) {
            return $element->getText();
        }

        // ListItem
        if ($element instanceof ListItem) {
            $textObject = $element->getTextObject();
            if ($textObject instanceof TextRun) {
                return $this->extractFromTextRun($textObject);
            }
            return '';
        }

        return '';
    }

    private function extractFromTextRun(TextRun $textRun): string
    {
        $text = '';
        foreach ($textRun->getElements() as $child) {
            if ($child instanceof Text) {
                $text .= $child->getText();
            }
        }
        return $text;
    }
}

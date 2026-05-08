<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Sop;
use App\Models\User;

class SopSeeder extends Seeder
{
    public function run(): void
    {
        $elfianus = User::where('email', 'elfianus@agrilit.id')->first();
        $siti     = User::where('email', 'siti.rahayu@agrilit.id')->first();

        // ============ SOP CABAI ============
        Sop::create([
            'author_id'   => $elfianus->id,
            'title'       => 'SOP Budidaya Cabai Merah Keriting - Dataran Rendah Malang',
            'slug'        => 'sop-cabai-merah-keriting-malang',
            'commodity'   => 'cabai',
            'description' => 'Panduan lengkap budidaya cabai merah keriting untuk wilayah dataran rendah Malang (0-500 mdpl) dengan target produksi 10-15 ton/ha.',
            'duration_days' => 120,
            'is_published'  => true,
            'monthly_calendar' => [
                [
                    'month'    => 1,
                    'week'     => 1,
                    'activity' => 'Persiapan lahan & persemaian',
                    'details'  => 'Bajak tanah, aplikasi pupuk kandang 20 ton/ha. Semai benih di tray semai dengan media campuran tanah:kompos:sekam 1:1:1. Perlakuan benih dengan fungisida mankozeb.',
                    'inputs'   => ['Pupuk kandang sapi', 'Benih cabai F1', 'Mankozeb'],
                ],
                [
                    'month'    => 1,
                    'week'     => 3,
                    'activity' => 'Pemasangan mulsa & bedengan',
                    'details'  => 'Buat bedengan lebar 110 cm, tinggi 30 cm. Pasang mulsa plastik hitam perak. Buat lubang tanam jarak 50x60 cm.',
                    'inputs'   => ['Mulsa plastik hitam perak', 'Bambu/kawat untuk penopang'],
                ],
                [
                    'month'    => 2,
                    'week'     => 1,
                    'activity' => 'Pemindahan bibit (transplanting)',
                    'details'  => 'Bibit siap dipindah saat berumur 25-30 hari (5-6 daun sejati). Siram persemaian dulu sebelum cabut. Tanam sore hari untuk mengurangi stres tanaman.',
                    'inputs'   => ['Bibit cabai umur 25-30 hari', 'Pupuk starter NPK 16-16-16'],
                ],
                [
                    'month'    => 2,
                    'week'     => 2,
                    'activity' => 'Pemasangan ajir & penyulaman',
                    'details'  => 'Pasang ajir bambu setinggi 80-100 cm per tanaman. Sulam tanaman yang mati atau pertumbuhannya abnormal.',
                    'inputs'   => ['Ajir bambu', 'Tali rafia'],
                ],
                [
                    'month'    => 2,
                    'week'     => 3,
                    'activity' => 'Pemupukan susulan pertama',
                    'details'  => 'Aplikasi NPK 16-16-16 dosis 5 gram/tanaman dikocorkan. Tambahkan insektisida preventif untuk thrips dan tungau.',
                    'inputs'   => ['NPK 16-16-16', 'Insektisida abamektin'],
                ],
                [
                    'month'    => 3,
                    'week'     => 1,
                    'activity' => 'Pengikatan & pemangkasan tunas air',
                    'details'  => 'Ikat batang utama ke ajir. Buang tunas-tunas air di bawah percabangan Y pertama. Cegah tanaman terlalu rimbun.',
                    'inputs'   => ['Tali rafia/karet', 'Gunting pangkas steril'],
                ],
                [
                    'month'    => 3,
                    'week'     => 2,
                    'activity' => 'Pemupukan susulan kedua',
                    'details'  => 'Saat mulai berbunga, tingkatkan kalium: KNO3 5 gram/tanaman + pupuk daun boron 2 ml/liter air.',
                    'inputs'   => ['KNO3 putih', 'Pupuk daun boron', 'Sprayer'],
                ],
                [
                    'month'    => 3,
                    'week'     => 3,
                    'activity' => 'Monitoring hama & penyakit',
                    'details'  => 'Intensifkan pengamatan: thrips pada bunga, antraknosa pada buah muda, virus kuning (kutu kebul). Catat titik serangan untuk pemetaan.',
                    'inputs'   => ['Senter', 'Buku catatan', 'Kamera HP'],
                ],
                [
                    'month'    => 4,
                    'week'     => 1,
                    'activity' => 'Panen perdana & pemupukan lanjut',
                    'details'  => 'Panen cabai merah yang sudah 80% merah. Panen pagi hari. Setelah panen pertama, berikan pupuk NPK + ZK untuk memacu flush berikutnya.',
                    'inputs'   => ['Keranjang panen', 'NPK + ZK (Kalium Sulfat)'],
                ],
                [
                    'month'    => 4,
                    'week'     => 2,
                    'activity' => 'Panen rutin & perawatan',
                    'details'  => 'Panen dilakukan setiap 3-5 hari. Lanjutkan pemupukan dan penyemprotan pestisida sesuai kondisi lapangan. Target panen sampai bulan ke-6.',
                    'inputs'   => ['Pestisida sesuai OPT', 'Pupuk daun'],
                ],
            ],
            'inputs_needed' => [
                ['name' => 'Benih cabai F1 (Lado/Gada)', 'dose' => '150-200 gram/ha', 'timing' => 'Sebelum semai'],
                ['name' => 'Pupuk kandang sapi', 'dose' => '20 ton/ha', 'timing' => 'Saat olah tanah'],
                ['name' => 'NPK 16-16-16', 'dose' => '300 kg/ha total', 'timing' => 'Bertahap setiap 2 minggu'],
                ['name' => 'KNO3', 'dose' => '100 kg/ha total', 'timing' => 'Saat fase generatif'],
                ['name' => 'Fungisida mankozeb', 'dose' => '2 gram/liter', 'timing' => 'Preventif 7-10 hari sekali'],
                ['name' => 'Insektisida abamektin', 'dose' => '1 ml/liter', 'timing' => 'Saat ada serangan thrips/tungau'],
                ['name' => 'Mulsa plastik hitam perak', 'dose' => '10 roll/ha', 'timing' => 'Sebelum tanam'],
            ],
        ]);

        // ============ SOP KENTANG ============
        Sop::create([
            'author_id'   => $elfianus->id,
            'title'       => 'SOP Budidaya Kentang Granola - Dataran Tinggi Malang',
            'slug'        => 'sop-kentang-granola-malang',
            'commodity'   => 'kentang',
            'description' => 'Panduan budidaya kentang varietas Granola untuk wilayah dataran tinggi Malang (>1000 mdpl) seperti Pujon, Ngantang, dan Tumpang.',
            'duration_days' => 100,
            'is_published'  => true,
            'monthly_calendar' => [
                [
                    'month'    => 1,
                    'week'     => 1,
                    'activity' => 'Persiapan bibit & lahan',
                    'details'  => 'Siapkan bibit G2/G3 bersertifikat. Potong umbi bibit (60-80 gram) 2-3 hari sebelum tanam, rendam dalam fungisida. Bajak lahan sedalam 30-40 cm.',
                    'inputs'   => ['Bibit kentang G2/G3', 'Fungisida berbahan aktif mankozeb', 'Kapur dolomit (jika pH<5.5)'],
                ],
                [
                    'month'    => 1,
                    'week'     => 2,
                    'activity' => 'Pembuatan bedengan & pemupukan dasar',
                    'details'  => 'Bedengan lebar 70-80 cm, tinggi 30-40 cm. Tugal lubang tanam 30x70 cm. Masukkan pupuk dasar: SP-36 + KCl + pupuk kandang per lubang.',
                    'inputs'   => ['SP-36 200 kg/ha', 'KCl 150 kg/ha', 'Pupuk kandang 20 ton/ha'],
                ],
                [
                    'month'    => 1,
                    'week'     => 2,
                    'activity' => 'Penanaman',
                    'details'  => 'Tanam bibit dengan mata tunas menghadap ke atas, kedalaman 10 cm. Tutup dengan tanah gembur. Pasang mulsa plastik hitam perak setelah tanam.',
                    'inputs'   => ['Bibit kentang', 'Mulsa plastik'],
                ],
                [
                    'month'    => 1,
                    'week'     => 4,
                    'activity' => 'Pemupukan susulan 1 & pengendalian gulma',
                    'details'  => 'Tanaman sudah 15-20 cm. Berikan Urea 100 kg/ha. Bumbun tanaman (naikkan tanah ke pangkal batang). Bersihkan gulma di luar mulsa.',
                    'inputs'   => ['Urea 100 kg/ha', 'Cangkul/kored'],
                ],
                [
                    'month'    => 2,
                    'week'     => 2,
                    'activity' => 'Bumbun kedua & semprot preventif late blight',
                    'details'  => 'Bumbun kedua setinggi 10-15 cm. Semprotkan fungisida sistemik metalaksil+mankozeb. Ini adalah periode kritis serangan late blight di Malang.',
                    'inputs'   => ['Fungisida metalaksil+mankozeb', 'Sprayer punggung'],
                ],
                [
                    'month'    => 2,
                    'week'     => 4,
                    'activity' => 'Pemupukan susulan 2',
                    'details'  => 'Saat awal pembentukan umbi. Berikan NPK Mutiara 16-16-16 sebanyak 200 kg/ha. Semprotkan pupuk daun kalium tinggi.',
                    'inputs'   => ['NPK Mutiara 200 kg/ha', 'Pupuk daun KNO3'],
                ],
                [
                    'month'    => 3,
                    'week'     => 2,
                    'activity' => 'Monitoring intensif & hentikan pemupukan N',
                    'details'  => 'Hentikan pupuk nitrogen. Fokus kalium untuk pembesaran umbi. Pantau late blight setiap 2 hari. Potong dan bakar daun bergejala berat.',
                    'inputs'   => ['KSO4 (Kalium Sulfat)', 'Fungisida preventif'],
                ],
                [
                    'month'    => 3,
                    'week'     => 4,
                    'activity' => 'Pemotongan batang (defoliasi) & persiapan panen',
                    'details'  => 'Potong batang 10 hari sebelum panen untuk mengeraskan kulit umbi dan menghindari penularan penyakit dari batang ke umbi. Hentikan irigasi.',
                    'inputs'   => ['Sabit/parang steril'],
                ],
                [
                    'month'    => 4,
                    'week'     => 1,
                    'activity' => 'Panen',
                    'details'  => 'Panen saat cuaca cerah. Cangkul hati-hati agar umbi tidak terluka. Sortir langsung di lapangan: umbi sehat, umbi busuk, umbi kecil. Angin-anginkan di tempat teduh 2-3 hari sebelum disimpan.',
                    'inputs'   => ['Cangkul', 'Keranjang sortasi', 'Karung'],
                ],
            ],
            'inputs_needed' => [
                ['name' => 'Bibit kentang G2/G3', 'dose' => '1.5-2 ton/ha', 'timing' => 'Sebelum tanam'],
                ['name' => 'Pupuk kandang', 'dose' => '20 ton/ha', 'timing' => 'Saat olah tanah'],
                ['name' => 'SP-36', 'dose' => '200 kg/ha', 'timing' => 'Pupuk dasar'],
                ['name' => 'KCl', 'dose' => '150 kg/ha', 'timing' => 'Pupuk dasar'],
                ['name' => 'Urea', 'dose' => '200 kg/ha total', 'timing' => 'Bertahap susulan 1 & 2'],
                ['name' => 'Fungisida metalaksil+mankozeb', 'dose' => '2 gram/liter', 'timing' => 'Preventif 5-7 hari sekali'],
            ],
        ]);

        // ============ SOP JAGUNG ============
        Sop::create([
            'author_id'   => $siti->id,
            'title'       => 'SOP Budidaya Jagung Hibrida - Lahan Kering Malang',
            'slug'        => 'sop-jagung-hibrida-malang',
            'commodity'   => 'jagung',
            'description' => 'Panduan budidaya jagung hibrida untuk lahan kering di Malang dengan target hasil 8-10 ton/ha pipilan kering.',
            'duration_days' => 100,
            'is_published'  => true,
            'monthly_calendar' => [
                [
                    'month'    => 1,
                    'week'     => 1,
                    'activity' => 'Persiapan lahan & penanaman',
                    'details'  => 'Bajak tanah, buat alur tanam jarak 75 cm antar baris. Seed treatment benih dengan metalaksil untuk pencegahan bulai. Tanam 1-2 benih per lubang, kedalaman 3-5 cm.',
                    'inputs'   => ['Benih jagung hibrida (Pioneer/NK)', 'Metalaksil seed treatment', 'Pupuk dasar Phonska'],
                ],
                [
                    'month'    => 1,
                    'week'     => 2,
                    'activity' => 'Penyulaman & penjarangan',
                    'details'  => 'Sulam benih yang tidak tumbuh. Jika 2 benih tumbuh, sisakan 1 tanaman terkuat. Monitoring gejala bulai sejak dini.',
                    'inputs'   => ['Benih cadangan', 'Fungisida sistemik jika ada bulai'],
                ],
                [
                    'month'    => 1,
                    'week'     => 3,
                    'activity' => 'Pemupukan susulan 1 (21 HST)',
                    'details'  => 'Larikan Urea 150 kg/ha di sisi baris tanaman. Tutup dengan tanah. Bersihkan gulma secara manual atau gunakan herbisida pasca tumbuh.',
                    'inputs'   => ['Urea 150 kg/ha', 'Herbisida atrazin (opsional)'],
                ],
                [
                    'month'    => 2,
                    'week'     => 1,
                    'activity' => 'Pembumbunan & pemupukan susulan 2 (45 HST)',
                    'details'  => 'Bumbun tanaman untuk memperkuat akar dan cegah rebah. Larikan Urea 100 kg/ha. Periksa gejala hawar daun (bercak coklat memanjang).',
                    'inputs'   => ['Urea 100 kg/ha', 'Cangkul'],
                ],
                [
                    'month'    => 2,
                    'week'     => 3,
                    'activity' => 'Fase pembungaan - monitoring ketat',
                    'details'  => 'Fase kritis: jaga kelembaban tanah saat muncul bunga jantan (rambut jagung). Kekurangan air saat ini menurunkan hasil drastis. Siram jika tidak hujan >7 hari.',
                    'inputs'   => ['Air irigasi jika perlu'],
                ],
                [
                    'month'    => 3,
                    'week'     => 2,
                    'activity' => 'Fase pengisian biji',
                    'details'  => 'Biji mulai berisi. Lindungi dari serangan penggerek tongkol dengan insektisida berbahan aktif klorpirifos pada sutra jagung. Pasang perangkap tikus jika perlu.',
                    'inputs'   => ['Insektisida klorpirifos', 'Perangkap tikus'],
                ],
                [
                    'month'    => 3,
                    'week'     => 4,
                    'activity' => 'Panen',
                    'details'  => 'Panen saat kelobot sudah kering dan biji keras (90-100 HST). Tanda siap panen: kuku tidak bisa menembus biji. Jemur segera untuk turunkan kadar air ke <14% sebelum disimpan.',
                    'inputs'   => ['Alat panen', 'Terpal jemur', 'Karung'],
                ],
            ],
            'inputs_needed' => [
                ['name' => 'Benih jagung hibrida', 'dose' => '15-20 kg/ha', 'timing' => 'Tanam'],
                ['name' => 'Phonska (15-15-15)', 'dose' => '300 kg/ha', 'timing' => 'Pupuk dasar'],
                ['name' => 'Urea', 'dose' => '300 kg/ha total', 'timing' => 'Susulan 21 HST dan 45 HST'],
                ['name' => 'Metalaksil (seed treatment)', 'dose' => '2 gram/kg benih', 'timing' => 'Sebelum tanam'],
            ],
        ]);
    }
}
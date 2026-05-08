<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID pakar & admin untuk author
        $elfianus = User::where('email', 'elfianus@agrilit.id')->first();
        $siti     = User::where('email', 'siti.rahayu@agrilit.id')->first();
        $admin    = User::where('email', 'admin@agrilit.id')->first();

        $articles = [

            // ============ CABAI ============
            [
                'author_id'    => $elfianus->id,
                'title'        => 'Mengenal Penyakit Antraknosa pada Cabai dan Cara Pengendaliannya',
                'commodity'    => 'cabai',
                'category'     => 'pengendalian',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'content'      => '
<p>Antraknosa adalah penyakit jamur yang paling umum menyerang tanaman cabai di Indonesia. Penyakit ini disebabkan oleh jamur <em>Colletotrichum capsici</em> dan <em>Colletotrichum acutatum</em>.</p>

<h2>Gejala</h2>
<p>Gejala awal berupa bercak coklat kehitaman pada buah cabai yang sudah tua atau matang. Bercak meluas dan membentuk cekungan yang khas. Pada kondisi lembab, terlihat massa spora berwarna merah salmon di tengah bercak.</p>

<h2>Penyebab dan Kondisi Serangan</h2>
<p>Jamur berkembang optimal pada suhu 25-28°C dengan kelembaban di atas 80%. Serangan paling parah terjadi pada musim hujan atau saat irigasi berlebihan.</p>

<h2>Cara Pengendalian</h2>
<ul>
    <li>Gunakan benih sehat bersertifikat bebas patogen</li>
    <li>Rotasi tanaman dengan non-solanaceae selama minimal 2 musim</li>
    <li>Aplikasi fungisida berbahan aktif mankozeb atau propineb secara preventif setiap 7-10 hari</li>
    <li>Pangkas dan musnahkan buah yang terinfeksi agar tidak menjadi sumber inokulum</li>
    <li>Perbaiki drainase lahan untuk mengurangi kelembaban tanah</li>
</ul>

<h2>Pencegahan Jangka Panjang</h2>
<p>Pilih varietas tahan seperti Lado F1 atau Gada F1 yang memiliki ketahanan moderat terhadap antraknosa. Kombinasi varietas tahan dan pengendalian kimiawi terbukti paling efektif di lahan Malang.</p>
                ',
            ],

            [
                'author_id'    => $siti->id,
                'title'        => 'Pemupukan Cabai Fase Generatif untuk Hasil Panen Optimal',
                'commodity'    => 'cabai',
                'category'     => 'pemupukan',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'content'      => '
<p>Fase generatif cabai dimulai sejak munculnya bunga pertama hingga panen. Pada fase ini, kebutuhan unsur hara berbeda signifikan dibandingkan fase vegetatif.</p>

<h2>Kebutuhan Hara Fase Generatif</h2>
<p>Tanaman membutuhkan lebih banyak Kalium (K) untuk mendukung pembentukan buah dan meningkatkan kualitas hasil. Rasio N:P:K yang direkomendasikan adalah 1:0.5:2.</p>

<h2>Jadwal Pemupukan</h2>
<ul>
    <li><strong>Minggu ke-6:</strong> Aplikasi NPK 16-16-16 dosis 2 gram/tanaman</li>
    <li><strong>Minggu ke-8:</strong> KNO3 (kalium nitrat) 3 gram/tanaman</li>
    <li><strong>Minggu ke-10:</strong> Ulang NPK + tambahan pupuk daun boron</li>
</ul>

<h2>Catatan Penting</h2>
<p>Hindari pemupukan nitrogen berlebihan saat fase generatif karena memacu pertumbuhan vegetatif yang mengorbankan pembungaan. Selalu lakukan pemupukan pada sore hari atau pagi hari dan pastikan tanah dalam kondisi lembab.</p>
                ',
            ],

            // ============ KENTANG ============
            [
                'author_id'    => $elfianus->id,
                'title'        => 'Busuk Daun (Late Blight) pada Kentang: Ancaman Terbesar Petani Malang',
                'commodity'    => 'kentang',
                'category'     => 'pengendalian',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'content'      => '
<p>Busuk daun atau <em>late blight</em> yang disebabkan oleh <em>Phytophthora infestans</em> adalah penyakit paling destruktif pada kentang. Penyakit inilah yang memicu kelaparan besar di Irlandia pada tahun 1840-an.</p>

<h2>Gejala di Lapangan</h2>
<p>Gejala awal muncul sebagai bercak basah kehijauan pada tepi daun, terutama di pagi hari saat embun masih ada. Dalam 24-48 jam bercak berubah coklat kehitaman dengan tepi kuning. Pada sisi bawah daun tampak lapisan putih seperti kapas yang merupakan sporangia jamur.</p>

<h2>Kondisi Ideal Serangan</h2>
<p>Serangan sangat cepat pada suhu 10-20°C dan kelembaban di atas 90%. Di dataran tinggi Malang seperti Pujon dan Ngantang, kondisi ini sering terjadi sepanjang tahun.</p>

<h2>Strategi Pengendalian Terpadu</h2>
<ul>
    <li>Tanam bibit G0/G1 bersertifikat bebas penyakit</li>
    <li>Semprotkan fungisida sistemik (metalaksil+mankozeb) preventif sejak 2 minggu setelah tanam</li>
    <li>Interval semprot 5-7 hari saat musim hujan, 10-14 hari saat kemarau</li>
    <li>Jangan semprot saat menjelang hujan karena fungisida akan larut</li>
    <li>Gunakan mulsa plastik hitam perak untuk menekan kelembaban tanah</li>
</ul>
                ',
            ],

            [
                'author_id'    => $admin->id,
                'title'        => 'Panduan Persiapan Lahan Kentang di Dataran Tinggi Malang',
                'commodity'    => 'kentang',
                'category'     => 'budidaya',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'content'      => '
<p>Kentang tumbuh optimal pada ketinggian 1.000-3.000 mdpl dengan suhu 15-20°C. Wilayah Pujon, Ngantang, dan Kasembon di Malang sangat ideal untuk budidaya kentang berkualitas tinggi.</p>

<h2>Syarat Tanah</h2>
<p>Kentang membutuhkan tanah gembur, berdrainase baik, dan kaya bahan organik dengan pH 5.5-6.5. Tanah liat berat menyebabkan umbi bentuk tidak beraturan dan rentan busuk.</p>

<h2>Tahapan Persiapan Lahan</h2>
<ol>
    <li>Bajak tanah sedalam 30-40 cm, biarkan terjemur 1-2 minggu untuk membunuh patogen</li>
    <li>Aplikasi kapur dolomit 1-2 ton/ha jika pH di bawah 5.5</li>
    <li>Tambahkan pupuk kandang matang 20-30 ton/ha dan campurkan merata</li>
    <li>Buat bedengan lebar 70-80 cm, tinggi 30-40 cm, jarak antar bedengan 30 cm</li>
    <li>Pasang mulsa plastik hitam perak minimal 1 minggu sebelum tanam</li>
</ol>

<h2>Jarak dan Waktu Tanam</h2>
<p>Jarak tanam 30x70 cm. Waktu tanam ideal di Malang adalah April-Mei (awal kemarau) dan September-Oktober (akhir musim hujan), menghindari puncak curah hujan yang memperparah serangan late blight.</p>
                ',
            ],

            // ============ JAGUNG ============
            [
                'author_id'    => $siti->id,
                'title'        => 'Penyakit Bulai pada Jagung dan Penanganan Cepat di Lapangan',
                'commodity'    => 'jagung',
                'category'     => 'pengendalian',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'content'      => '
<p>Bulai jagung yang disebabkan oleh <em>Peronosclerospora maydis</em> adalah penyakit utama yang dapat menyebabkan gagal panen total jika tidak ditangani sejak dini.</p>

<h2>Ciri Khas Gejala</h2>
<p>Daun muda berwarna hijau pucat hingga kuning, tumbuh kaku dan tegak (tidak melengkung normal). Pada pagi hari tampak lapisan tepung putih di permukaan daun bagian bawah. Tanaman yang terinfeksi berat tidak akan membentuk tongkol.</p>

<h2>Deteksi Dini</h2>
<p>Amati tanaman jagung mulai hari ke-7 setelah tanam. Cabut dan musnahkan tanaman bergejala segera untuk mencegah penyebaran spora ke tanaman sehat di sekitarnya.</p>

<h2>Pengendalian</h2>
<ul>
    <li>Seed treatment dengan metalaksil sebelum tanam adalah pencegahan paling efektif</li>
    <li>Gunakan varietas tahan bulai: Pioneer 27, NK 212, atau Bisi 18</li>
    <li>Rotasi tanaman dengan padi atau kedelai minimal 1 musim</li>
    <li>Sanitasi lahan: bersihkan sisa tanaman jagung terinfeksi dari musim sebelumnya</li>
</ul>
                ',
            ],

            [
                'author_id'    => $elfianus->id,
                'title'        => 'Teknik Pemupukan Berimbang untuk Jagung Hibrida',
                'commodity'    => 'jagung',
                'category'     => 'pemupukan',
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'content'      => '
<p>Jagung hibrida berpotensi hasil 8-12 ton/ha pipilan kering, tetapi potensi ini hanya tercapai dengan pemupukan yang tepat waktu dan tepat dosis.</p>

<h2>Kebutuhan Hara Jagung</h2>
<p>Untuk hasil 8 ton/ha pipilan kering, jagung membutuhkan sekitar 200 kg N, 80 kg P2O5, dan 120 kg K2O per hektar sepanjang musim tanam.</p>

<h2>Jadwal Pemupukan 3 Kali</h2>
<ul>
    <li><strong>Pupuk dasar (tanam):</strong> 200 kg/ha Phonska + 100 kg/ha SP-36, ditugal di samping benih</li>
    <li><strong>Susulan 1 (umur 21 HST):</strong> 150 kg/ha Urea, larikan di sisi barisan tanaman</li>
    <li><strong>Susulan 2 (umur 45 HST):</strong> 100 kg/ha Urea, larikan dan tutup dengan tanah</li>
</ul>

<h2>Tips Lapangan</h2>
<p>Pemupukan susulan dilakukan saat tanah lembab agar pupuk cepat larut dan tersedia. Hindari pemupukan saat terik matahari. Jika menggunakan pupuk organik, kurangi dosis kimia sebesar 25-30% untuk menghindari akumulasi garam.</p>
                ',
            ],

            // ============ UMUM ============
            [
                'author_id'    => $admin->id,
                'title'        => 'Cara Membaca dan Menggunakan Aplikasi AgriLit untuk Petani Pemula',
                'commodity'    => 'umum',
                'category'     => 'umum',
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'content'      => '
<p>Selamat datang di AgriLit! Panduan ini akan membantu Anda memahami fitur-fitur utama aplikasi dan cara memanfaatkannya semaksimal mungkin untuk mendukung usaha pertanian Anda.</p>

<h2>Fitur Utama AgriLit</h2>
<ul>
    <li><strong>Literasi:</strong> Kumpulan artikel pertanian yang disusun oleh pakar, bisa dibaca meski tanpa internet</li>
    <li><strong>SOP Budidaya:</strong> Kalender tanam bulanan untuk cabai, kentang, dan jagung</li>
    <li><strong>Forum:</strong> Tempat bertanya langsung kepada pakar dan sesama petani</li>
    <li><strong>Scan Penyakit:</strong> Foto daun tanaman Anda dan AI akan membantu mendiagnosa penyakitnya</li>
</ul>

<h2>Tips Penggunaan Fitur Scan</h2>
<p>Untuk hasil diagnosis terbaik, foto daun yang menunjukkan gejala terjelas. Pastikan pencahayaan cukup, latar belakang polos, dan jarak kamera 15-20 cm dari daun. Foto lebih dari satu daun bergejala untuk hasil yang lebih akurat.</p>
                ',
            ],
        ];

        foreach ($articles as $data) {
            $title = $data['title'];
            Article::create(array_merge($data, [
                'slug'    => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
                'excerpt' => \Illuminate\Support\Str::limit(strip_tags($data['content']), 150),
                'views'   => rand(10, 300),
            ]));
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $budi    = User::where('email', 'budi@agrilit.id')->first();
        $slamet  = User::where('email', 'slamet@agrilit.id')->first();
        $wati    = User::where('email', 'wati@agrilit.id')->first();
        $hendra  = User::where('email', 'hendra@agrilit.id')->first();
        $elfianus = User::where('email', 'elfianus@agrilit.id')->first();
        $siti    = User::where('email', 'siti.rahayu@agrilit.id')->first();

        // ===== POST 1: Sudah dijawab pakar =====
        $post1 = ForumPost::create([
            'user_id'     => $budi->id,
            'title'       => 'Cabai saya daunnya menggulung ke bawah dan berwarna kuning, kenapa ya?',
            'content'     => 'Pak/Bu pakar, tanaman cabai saya berumur 45 hari. Sejak seminggu lalu daunnya mulai menggulung ke bawah dan warnanya menguning, mulai dari daun tua. Pertumbuhannya juga jadi lambat. Sudah saya siram lebih banyak tapi tidak membaik. Apa penyebabnya dan bagaimana cara mengatasinya? Terima kasih.',
            'commodity'   => 'cabai',
            'status'      => 'approved',
            'is_answered' => true,
            'views'       => 142,
        ]);

        ForumReply::create([
            'post_id'         => $post1->id,
            'user_id'         => $elfianus->id,
            'content'         => 'Berdasarkan gejala yang Anda ceritakan, kemungkinan besar tanaman cabai Anda mengalami defisiensi Magnesium (Mg) yang dikombinasikan dengan serangan tungau merah (Tetranychus urticae). Daun menggulung ke bawah adalah ciri khas serangan tungau, sedangkan menguning dari daun tua menunjukkan kekurangan Mg yang bersifat mobile (pindah dari daun tua ke daun muda).

Langkah penanganan yang saya sarankan:
1. Semprotkan akarisida berbahan aktif abamektin (1 ml/liter) ke seluruh permukaan daun, termasuk bagian bawah daun
2. Semprotkan pupuk daun mengandung MgSO4 (Kieserit) dosis 5 gram/liter bersamaan
3. Hindari menyiram berlebihan karena tanah yang terlalu basah justru menghambat penyerapan Mg
4. Ulangi semprot setelah 5 hari

Perhatikan juga: apakah ada bekas gigitan halus seperti titik-titik kecil di permukaan daun? Jika ya, itu konfirmasi serangan tungau.',
            'is_expert_answer' => true,
            'upvotes'         => 23,
        ]);

        ForumReply::create([
            'post_id' => $post1->id,
            'user_id' => $slamet->id,
            'content' => 'Saya pernah mengalami hal serupa Pak Budi. Setelah disemprot abamektin 2 kali dalam seminggu, tanaman saya membaik. Coba cek bagian bawah daun pakai kaca pembesar, pasti ada tungau kecil berwarna merah.',
            'upvotes' => 8,
        ]);

        // ===== POST 2: Menunggu jawaban =====
        $post2 = ForumPost::create([
            'user_id'   => $wati->id,
            'title'     => 'Daun kentang saya ada bercak coklat berminyak di pinggir, ini late blight atau bukan?',
            'content'   => 'Saya tanam kentang di Tumpang, sudah 40 hari. Tadi pagi saya lihat ada beberapa daun yang pinggirnya ada bercak basah kecoklatan, dan kalau dipegang terasa agak berminyak. Cuaca di sini memang sedang hujan terus 5 hari terakhir. Saya khawatir ini late blight yang pernah saya baca di artikel AgriLit. Apa langkah pertama yang harus saya lakukan sekarang?',
            'commodity' => 'kentang',
            'status'    => 'approved',
            'views'     => 89,
        ]);

        ForumReply::create([
            'post_id' => $post2->id,
            'user_id' => $hendra->id,
            'content' => 'Bu Wati, saya juga petani kentang di Ngantang. Dari deskripsinya memang mirip late blight. Coba cek bagian bawah daunnya, kalau ada lapisan putih seperti kapas tipis, itu hampir pasti late blight. Langkah pertama saya biasanya langsung semprot fungisida sistemik.',
            'upvotes' => 5,
        ]);

        // ===== POST 3: Pending moderasi =====
        ForumPost::create([
            'user_id'   => $hendra->id,
            'title'     => 'Rekomendasi merek fungisida yang bagus untuk bulai jagung di Malang?',
            'content'   => 'Pak Elfianus atau Bu Siti, saya mau tanya merek fungisida yang paling efektif untuk menangani bulai pada jagung. Tahun lalu saya pakai metalaksil tapi hasilnya tidak terlalu signifikan. Apakah ada produk yang lebih baru dan lebih efektif? Terutama yang tersedia di toko pertanian daerah Malang.',
            'commodity' => 'jagung',
            'status'    => 'pending',
            'views'     => 3,
        ]);

        // ===== POST 4: Sudah dijawab =====
        $post4 = ForumPost::create([
            'user_id'     => $slamet->id,
            'title'       => 'Berapa kali idealnya panen cabai dalam satu musim tanam?',
            'content'     => 'Cabai saya sudah mulai panen perdana. Saya dengar kalau dirawat dengan baik bisa panen sampai 15-20 kali. Apakah itu benar? Apa kuncinya agar tanaman bisa produktif sampai panen ke-15 atau lebih?',
            'commodity'   => 'cabai',
            'status'      => 'approved',
            'is_answered' => true,
            'views'       => 211,
        ]);

        ForumReply::create([
            'post_id'         => $post4->id,
            'user_id'         => $siti->id,
            'content'         => 'Betul Pak Slamet, tanaman cabai yang sehat bisa dipanen 15-20 kali bahkan lebih dalam satu musim tanam yang bisa mencapai 6-8 bulan. Kunci utamanya ada 3:

Pertama, nutrisi berkelanjutan. Setiap selesai panen, segera berikan pupuk untuk mengembalikan energi tanaman. Kombinasi NPK + pupuk organik cair setiap 10-14 hari adalah standar yang saya rekomendasikan.

Kedua, pengendalian OPT konsisten. Jangan tunggu ada serangan baru mulai semprot. Jadwal semprot preventif yang disiplin jauh lebih murah daripada menangani serangan berat.

Ketiga, panen tepat waktu. Jangan biarkan buah terlalu tua di pohon karena akan menguras energi tanaman. Panen buah yang sudah 80% merah setiap 3-5 hari secara rutin.',
            'is_expert_answer' => true,
            'upvotes'         => 45,
        ]);
    }
}
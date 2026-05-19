@extends('layouts.app')
@section('title', 'Import Artikel dari DOCX')

@section('content')

    <div class="max-w-2xl">

        {{-- Info box --}}
        <div class="card mb-6 bg-blue-50 border-blue-200">
            <div class="flex gap-3">
                <div class="text-2xl">📄</div>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">
                        Import Artikel dari File Word (.docx)
                    </h3>
                    <p class="text-sm text-blue-700 mb-3">
                        Upload file Word dengan format template AgriLit.
                        Sistem akan otomatis mengekstrak judul, komoditas,
                        kategori, dan konten artikel.
                    </p>
                    <a href="{{ asset('templates/AgriLit_Template_Artikel.docx') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium
                          text-blue-700 hover:text-blue-900 underline">
                        ⬇️ Download Template AgriLit (.docx)
                    </a>
                </div>
            </div>
        </div>

        {{-- Format guide --}}
        <div class="card mb-6">
            <h3 class="font-semibold text-gray-800 mb-1">📋 Aturan Format</h3>
            <p class="text-sm text-gray-500 mb-4">
                Nama bagian konten <strong>bebas</strong> — sesuaikan dengan topik artikel.
            </p>

            <div class="space-y-3 text-sm">
                <div class="flex gap-3 items-start p-3 bg-red-50 rounded-lg border border-red-100">
                    <span class="text-red-500 font-bold flex-shrink-0">WAJIB</span>
                    <div>
                        <p class="font-medium text-gray-800">Heading 1 → Judul artikel</p>
                        <p class="text-gray-500">Tepat satu baris, tidak boleh lebih dari satu</p>
                    </div>
                </div>

                <div class="flex gap-3 items-start p-3 bg-red-50 rounded-lg border border-red-100">
                    <span class="text-red-500 font-bold flex-shrink-0">WAJIB</span>
                    <div>
                        <p class="font-medium text-gray-800">Heading 2 → META</p>
                        <p class="text-gray-500 mt-1">
                            Diikuti dua baris Normal:<br>
                            <code class="bg-white border px-1.5 py-0.5 rounded text-xs">
                                Komoditas: cabai
                            </code>
                            <span class="text-gray-400 text-xs ml-1">
                                (cabai / kentang / jagung / umum)
                            </span><br>
                            <code class="bg-white border px-1.5 py-0.5 rounded text-xs mt-1 inline-block">
                                Kategori: budidaya
                            </code>
                            <span class="text-gray-400 text-xs ml-1">
                                (budidaya / pemupukan / pengendalian / pascapanen / umum)
                            </span>
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start p-3 bg-green-50 rounded-lg border border-green-100">
                    <span class="text-green-600 font-bold flex-shrink-0">BEBAS</span>
                    <div>
                        <p class="font-medium text-gray-800">
                            Heading 2 → Nama bagian konten (apapun)
                        </p>
                        <p class="text-gray-500 mt-1">
                            Nama heading bebas, sesuaikan dengan topik:
                        </p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach (['PENDAHULUAN', 'GEJALA', 'PENYEBAB', 'PENGENDALIAN', 'PERSIAPAN LAHAN', 'TEKNIK PENYEMAIAN', 'PERAWATAN BIBIT', 'WAKTU PANEN', 'PROSES CURING', 'CARA PENYIMPANAN', 'PEMUPUKAN', 'KESIMPULAN'] as $contoh)
                                <span
                                    class="px-2 py-0.5 bg-white border border-green-200
                                     rounded text-xs text-green-700 font-mono">
                                    {{ $contoh }}
                                </span>
                            @endforeach
                            <span class="px-2 py-0.5 text-xs text-gray-400 italic">
                                dll...
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <span class="text-gray-500 font-bold flex-shrink-0">ISI</span>
                    <div>
                        <p class="font-medium text-gray-800">Normal → Isi konten</p>
                        <p class="text-gray-500">
                            Paragraf biasa atau bullet list bawaan Word.
                            Tidak perlu format khusus.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Contoh per topik --}}
            <div class="mt-5 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase
                   tracking-wider mb-3">Contoh
                    Susunan per Topik</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ([
            [
                'label' => '🌶️ Penyakit',
                'cat' => 'pengendalian',
                'items' => ['META', 'PENDAHULUAN', 'GEJALA', 'PENYEBAB', 'PENGENDALIAN', 'KESIMPULAN'],
            ],
            [
                'label' => '🌱 Budidaya',
                'cat' => 'budidaya',
                'items' => ['META', 'PERSIAPAN LAHAN', 'PEMILIHAN BENIH', 'TEKNIK PENYEMAIAN', 'PERAWATAN', 'PANEN'],
            ],
            [
                'label' => '📦 Pasca Panen',
                'cat' => 'pascapanen',
                'items' => ['META', 'WAKTU PANEN TEPAT', 'PROSES CURING', 'KONDISI PENYIMPANAN', 'KESALAHAN UMUM'],
            ],
        ] as $ex)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-semibold text-gray-700 mb-2">
                                {{ $ex['label'] }}
                                <span class="font-normal text-gray-400">
                                    ({{ $ex['cat'] }})
                                </span>
                            </p>
                            @foreach ($ex['items'] as $item)
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span
                                        class="text-xs {{ $item === 'META' ? 'text-red-500 font-bold' : 'text-green-600' }}">
                                        H2
                                    </span>
                                    <span class="text-xs text-gray-600 font-mono">
                                        {{ $item }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Upload form --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Upload File</h3>
            <form method="POST" action="{{ route('admin.articles.import.preview') }}" enctype="multipart/form-data">
                @csrf

                <div class="border-2 border-dashed border-gray-300 rounded-xl
                        p-8 text-center hover:border-primary-400
                        transition-colors cursor-pointer"
                    onclick="document.getElementById('docx_file').click()">
                    <div class="text-5xl mb-3">📄</div>
                    <p class="font-medium text-gray-700">
                        Klik untuk pilih file Word
                    </p>
                    <p class="text-sm text-gray-400 mt-1">
                        Format: .docx | Maks: 10MB
                    </p>
                    <input type="file" id="docx_file" name="docx_file" accept=".docx,.doc" class="hidden"
                        onchange="showFileName(this)">
                    <p id="fileName" class="mt-3 text-sm text-primary-600 font-medium hidden">
                    </p>
                </div>

                @error('docx_file')
                    <div
                        class="mt-3 p-3 bg-red-50 border border-red-200
                            rounded-lg text-sm text-red-700">
                        ❌ {{ $message }}
                    </div>
                @enderror

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary flex-1">
                        🔍 Parse & Preview Artikel
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function showFileName(input) {
                const el = document.getElementById('fileName');
                if (input.files.length > 0) {
                    el.textContent = '✅ ' + input.files[0].name;
                    el.classList.remove('hidden');
                }
            }
        </script>
    @endpush

@endsection

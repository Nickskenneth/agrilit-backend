@extends('layouts.app')
@section('title', $article ? 'Edit Artikel' : 'Tulis Artikel Baru')

@push('styles')
    {{-- Quill.js CSS --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        /* Container editor selaras dengan Tailwind */
        .ql-container {
            font-family: inherit;
            font-size: 0.9375rem;
            /* 15px */
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #D1D5DB;
            /* gray-300 */
        }

        .ql-toolbar {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #D1D5DB;
            background-color: #F9FAFB;
            /* gray-50 */
        }

        /* Highlight saat editor fokus */
        .ql-container.ql-focused,
        .ql-toolbar:has(+ .ql-container.ql-focused) {
            border-color: #16A34A;
            /* primary-600 */
            outline: none;
        }

        /* Tinggi editor */
        .ql-editor {
            min-height: 320px;
            max-height: 600px;
            overflow-y: auto;
            line-height: 1.75;
            color: #111827;
            /* gray-900 */
        }

        /* Placeholder */
        .ql-editor.ql-blank::before {
            color: #9CA3AF;
            /* gray-400 */
            font-style: normal;
        }

        /* Override border merah saat error */
        .editor-error .ql-container,
        .editor-error .ql-toolbar {
            border-color: #EF4444 !important;
        }

        /* Styling konten di dalam editor */
        .ql-editor h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
        }

        .ql-editor h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0.75rem 0 0.4rem;
        }

        .ql-editor p {
            margin-bottom: 0.5rem;
        }

        .ql-editor ul,
        .ql-editor ol {
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .ql-editor li {
            margin-bottom: 0.25rem;
        }

        .ql-editor a {
            color: #16A34A;
            text-decoration: underline;
        }

        .ql-editor blockquote {
            border-left: 4px solid #16A34A;
            padding-left: 1rem;
            color: #6B7280;
            margin: 0.75rem 0;
        }

        /* Image di dalam editor */
        .ql-editor img {
            max-width: 100%;
            border-radius: 0.5rem;
            margin: 0.5rem 0;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl">
        <form id="articleForm" method="POST"
            action="{{ $article ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($article)
                @method('PUT')
            @endif

            {{-- ===== CARD: INFO DASAR ===== --}}
            <div class="card space-y-5">
                <h2 class="font-semibold text-gray-800 text-base
                        border-b border-gray-100 pb-3">
                    Informasi Artikel
                </h2>

                {{-- Judul --}}
                <div>
                    <label class="form-label">
                        Judul Artikel
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $article?->title) }}"
                        class="form-input text-base font-medium
                           @error('title') border-red-500 @enderror"
                        placeholder="Contoh: Mengenal Penyakit Antraknosa pada Cabai" maxlength="255">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Komoditas & Kategori --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">
                            Komoditas
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="commodity" class="form-input @error('commodity') border-red-500 @enderror">
                            @foreach ([
            'cabai' => '🌶️ Cabai',
            'kentang' => '🥔 Kentang',
            'jagung' => '🌽 Jagung',
            'umum' => '📖 Umum',
        ] as $val => $label)
                                <option value="{{ $val }}" @selected(old('commodity', $article?->commodity) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('commodity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            Kategori
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="category" class="form-input @error('category') border-red-500 @enderror">
                            @foreach ([
            'budidaya' => 'Budidaya',
            'pemupukan' => 'Pemupukan',
            'pengendalian' => 'Pengendalian OPT',
            'pascapanen' => 'Pasca Panen',
            'umum' => 'Umum',
        ] as $val => $label)
                                <option value="{{ $val }}" @selected(old('category', $article?->category) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div>
                    <label class="form-label">Gambar Sampul</label>
                    @if ($article?->thumbnail)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                class="h-24 w-36 object-cover rounded-lg border border-gray-200" alt="Thumbnail saat ini">
                            <p class="text-xs text-gray-400">
                                Thumbnail saat ini.<br>
                                Upload baru untuk mengganti.
                            </p>
                        </div>
                    @endif
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="form-input @error('thumbnail') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-400">
                        Format: JPG, PNG, WebP. Maks 2MB.
                    </p>
                    @error('thumbnail')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ===== CARD: KONTEN (QUILL EDITOR) ===== --}}
            <div class="card space-y-3">
                <div class="flex items-center justify-between border-b
                        border-gray-100 pb-3">
                    <h2 class="font-semibold text-gray-800 text-base">
                        Konten Artikel
                        <span class="text-red-500">*</span>
                    </h2>
                    {{-- Toggle mode HTML mentah (untuk admin teknis) --}}
                    <button type="button" id="toggleHtmlMode"
                        class="text-xs text-gray-400 hover:text-gray-600
                           flex items-center gap-1 transition-colors">
                        <span id="toggleIcon">⌨️</span>
                        <span id="toggleLabel">Mode HTML</span>
                    </button>
                </div>

                {{-- Quill editor container --}}
                <div id="editorWrapper" class="{{ $errors->has('content') ? 'editor-error' : '' }}">
                    <div id="quillEditor"></div>
                </div>

                {{-- Fallback textarea (mode HTML mentah) --}}
                <div id="htmlModeWrapper" class="hidden">
                    <textarea id="htmlRawTextarea" rows="16" class="form-input font-mono text-sm"
                        placeholder="Tulis atau paste HTML di sini..."></textarea>
                    <p class="mt-1 text-xs text-gray-400">
                        Mode HTML mentah — untuk pengguna yang familiar dengan HTML.
                    </p>
                </div>

                {{-- Hidden input — nilai ini yang dikirim ke server --}}
                <input type="hidden" name="content" id="contentInput" value="{{ old('content', $article?->content) }}">

                @error('content')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Word / karakter counter --}}
                <div class="flex justify-between items-center pt-1">
                    <p class="text-xs text-gray-400">
                        💡 Gunakan toolbar di atas untuk format teks,
                        sisipkan gambar URL, atau tambahkan link.
                    </p>
                    <p class="text-xs text-gray-400">
                        <span id="charCount">0</span> karakter
                    </p>
                </div>
            </div>

            {{-- ===== CARD: PENGATURAN PUBLISH ===== --}}
            <div class="card">
                <h2
                    class="font-semibold text-gray-800 text-base
                        border-b border-gray-100 pb-3 mb-4">
                    Pengaturan Publikasi
                </h2>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                            @checked(old('is_published', $article?->is_published)) class="sr-only peer">
                        {{-- Custom toggle --}}
                        <div
                            class="w-10 h-6 bg-gray-200 peer-checked:bg-primary-600
                                rounded-full transition-colors duration-200
                                peer-focus:ring-2 peer-focus:ring-primary-300">
                        </div>
                        <div
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white
                                rounded-full shadow transition-transform duration-200
                                peer-checked:translate-x-4">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            Terbitkan artikel sekarang
                        </p>
                        <p class="text-xs text-gray-400">
                            Jika tidak aktif, artikel disimpan sebagai draft
                            dan tidak tampil di aplikasi mobile.
                        </p>
                    </div>
                </label>

                @if ($article?->published_at)
                    <p class="mt-3 text-xs text-gray-400">
                        Pertama diterbitkan:
                        {{ $article->published_at->format('d M Y, H:i') }}
                    </p>
                @endif
            </div>

            {{-- ===== ACTION BUTTONS ===== --}}
            <div class="flex items-center gap-3">
                <button type="submit" id="submitBtn" class="btn-primary flex items-center gap-2">
                    <span id="submitIcon">💾</span>
                    <span id="submitLabel">
                        {{ $article ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                    </span>
                </button>



                <a href="{{ route('admin.articles.index') }}" class="btn-secondary ml-auto">
                    Batal
                </a>
            </div>

        </form>
    </div>
@endsection


@push('scripts')
    {{-- Quill.js --}}
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ============================================================
            // 1. INISIALISASI QUILL
            // ============================================================
            const quill = new Quill('#quillEditor', {
                theme: 'snow',
                placeholder: 'Tulis konten artikel di sini...',
                modules: {
                    toolbar: {
                        container: [
                            // Heading
                            [{
                                header: [2, 3, false]
                            }],
                            // Format teks
                            ['bold', 'italic', 'underline'],
                            // List
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            // Indent
                            [{
                                indent: '-1'
                            }, {
                                indent: '+1'
                            }],
                            // Link & gambar
                            ['link', 'image', 'video'],
                            // Blockquote & code
                            ['blockquote'],
                            // Hapus format
                            ['clean'],
                        ],
                        // Handler khusus untuk insert image via URL
                        handlers: {
                            image: imageHandler,
                            video: videoHandler,
                        },
                    },
                },
            });

            // ============================================================
            // 2. ISI KONTEN AWAL (saat edit artikel)
            // ============================================================
            const initialContent = document.getElementById('contentInput').value;
            if (initialContent && initialContent.trim() !== '') {
                // Quill menerima Delta atau HTML — gunakan pasteHTML
                const delta = quill.clipboard.convert(initialContent);
                quill.setContents(delta, 'silent');
            }

            // ============================================================
            // 3. UPDATE HIDDEN INPUT & COUNTER saat konten berubah
            // ============================================================
            const contentInput = document.getElementById('contentInput');
            const charCounter = document.getElementById('charCount');

            quill.on('text-change', function() {
                const html = quill.root.innerHTML;

                // Jika editor kosong, kirim string kosong (bukan "<p><br></p>")
                contentInput.value = quill.getText().trim() === '' ? '' : html;

                // Update karakter counter (plain text tanpa tag)
                charCounter.textContent = quill.getText().trim().length.toLocaleString();
            });

            // Trigger sekali untuk isi counter saat edit
            if (initialContent) {
                charCounter.textContent = quill.getText().trim().length.toLocaleString();
            }

            // ============================================================
            // 4. IMAGE HANDLER — Insert gambar via URL
            //    (tidak upload file langsung ke editor agar konten tetap
            //     bersih dan ringan; upload gambar besar via thumbnail)
            // ============================================================
            function imageHandler() {
                const url = prompt('Masukkan URL gambar:');
                if (!url || url.trim() === '') return;

                // Validasi URL sederhana
                if (!url.startsWith('http') && !url.startsWith('/')) {
                    alert('URL tidak valid. Gunakan URL yang dimulai dengan http/https.');
                    return;
                }

                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', url.trim(), Quill.sources.USER);
                quill.setSelection(range.index + 1, Quill.sources.SILENT);
            }

            // ============================================================
            // 4B. VIDEO HANDLER — Insert video via URL dengan prompt
            // ============================================================
            function videoHandler() {
                let url = prompt('Masukkan URL video YouTube:');
                if (!url || url.trim() === '') return;

                // Ubah link YouTube biasa menjadi link Embed agar bisa diputar
                let embedUrl = url;
                if (url.includes('youtube.com/watch?v=')) {
                    embedUrl = url.replace('watch?v=', 'embed/');
                    let ampersandPosition = embedUrl.indexOf('&');
                    if (ampersandPosition !== -1) {
                        embedUrl = embedUrl.substring(0, ampersandPosition);
                    }
                } else if (url.includes('youtu.be/')) {
                    embedUrl = url.replace('youtu.be/', 'youtube.com/embed/');
                }

                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'video', embedUrl, Quill.sources.USER);
                quill.setSelection(range.index + 1, Quill.sources.SILENT);
            }

            // ============================================================
            // 5. FORM SUBMIT — Pastikan hidden input terisi sebelum submit
            // ============================================================
            const form = document.getElementById('articleForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const html = quill.root.innerHTML;
                const plainText = quill.getText().trim();

                // Paksa sync nilai terbaru ke hidden input
                contentInput.value = plainText === '' ? '' : html;

                // Validasi konten tidak kosong
                if (plainText === '') {
                    e.preventDefault();
                    // Tambahkan class error pada editor
                    document.getElementById('editorWrapper')
                        .classList.add('editor-error');
                    quill.focus();

                    // Tampilkan pesan error sementara
                    showEditorError('Konten artikel tidak boleh kosong.');
                    return;
                }

                // Loading state pada tombol submit
                document.getElementById('submitIcon').textContent = '⏳';
                document.getElementById('submitLabel').textContent = 'Menyimpan...';
                submitBtn.disabled = true;
            });

            // ============================================================
            // 6. TOGGLE MODE HTML MENTAH
            //    Admin teknis bisa switch ke textarea HTML jika diperlukan
            // ============================================================
            const toggleBtn = document.getElementById('toggleHtmlMode');
            const editorWrapper = document.getElementById('editorWrapper');
            const htmlModeWrapper = document.getElementById('htmlModeWrapper');
            const htmlRawTextarea = document.getElementById('htmlRawTextarea');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleLabel = document.getElementById('toggleLabel');
            let isHtmlMode = false;

            toggleBtn.addEventListener('click', function() {
                isHtmlMode = !isHtmlMode;

                if (isHtmlMode) {
                    // Switch ke HTML mode — ambil konten dari Quill
                    htmlRawTextarea.value = quill.root.innerHTML;
                    editorWrapper.classList.add('hidden');
                    htmlModeWrapper.classList.remove('hidden');
                    toggleIcon.textContent = '✏️';
                    toggleLabel.textContent = 'Mode Visual';
                } else {
                    // Switch kembali ke Visual mode — ambil konten dari textarea
                    const rawHtml = htmlRawTextarea.value;
                    const delta = quill.clipboard.convert(rawHtml);
                    quill.setContents(delta, 'silent');
                    editorWrapper.classList.remove('hidden');
                    htmlModeWrapper.classList.add('hidden');
                    toggleIcon.textContent = '⌨️';
                    toggleLabel.textContent = 'Mode HTML';
                }
            });

            // Saat di HTML mode, sync textarea ke hidden input juga
            htmlRawTextarea.addEventListener('input', function() {
                contentInput.value = htmlRawTextarea.value;
                charCounter.textContent = htmlRawTextarea.value
                    .replace(/<[^>]*>/g, '').trim().length.toLocaleString();
            });

            // ============================================================
            // 7. HELPER — Tampilkan pesan error editor
            // ============================================================
            function showEditorError(message) {
                // Hapus error lama jika ada
                const existing = document.getElementById('editorErrorMsg');
                if (existing) existing.remove();

                const err = document.createElement('p');
                err.id = 'editorErrorMsg';
                err.className = 'text-xs text-red-600 mt-1';
                err.textContent = message;
                document.getElementById('editorWrapper').after(err);

                // Auto-hilang setelah 4 detik
                setTimeout(() => err.remove(), 4000);
            }

            // Hapus class error saat user mulai mengetik
            quill.on('text-change', function() {
                document.getElementById('editorWrapper')
                    .classList.remove('editor-error');
                const existing = document.getElementById('editorErrorMsg');
                if (existing) existing.remove();
            });

        });
    </script>
@endpush

@extends('layouts.app')
@section('title', $article ? 'Edit Artikel' : 'Tulis Artikel Baru')

@section('content')

    <div class="max-w-4xl">
        <form method="POST"
            action="{{ $article ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($article)
                @method('PUT')
            @endif

            <div class="card space-y-5">

                <div>
                    <label class="form-label">Judul Artikel <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $article?->title) }}"
                        class="form-input text-lg font-medium @error('title') border-red-500 @enderror"
                        placeholder="Contoh: Mengenal Penyakit Antraknosa pada Cabai">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Komoditas <span class="text-red-500">*</span></label>
                        <select name="commodity" class="form-input @error('commodity') border-red-500 @enderror">
                            @foreach (['cabai' => '🌶️ Cabai', 'kentang' => '🥔 Kentang', 'jagung' => '🌽 Jagung', 'umum' => '📖 Umum'] as $val => $label)
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
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="form-input @error('category') border-red-500 @enderror">
                            @foreach (['budidaya', 'pemupukan', 'pengendalian', 'pascapanen', 'umum'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $article?->category) === $cat)>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Gambar Sampul</label>
                    @if ($article?->thumbnail)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                class="h-32 w-auto rounded-lg object-cover" alt="Thumbnail">
                            <p class="text-xs text-gray-400 mt-1">Thumbnail saat ini. Upload baru untuk mengganti.</p>
                        </div>
                    @endif
                    <input type="file" name="thumbnail" accept="image/*"
                        class="form-input @error('thumbnail') border-red-500 @enderror">
                    @error('thumbnail')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Konten Artikel <span class="text-red-500">*</span></label>
                    <textarea name="content" id="content" rows="20"
                        class="form-input font-mono text-sm @error('content') border-red-500 @enderror"
                        placeholder="Tulis konten artikel dalam format HTML...">{{ old('content', $article?->content) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">
                        Mendukung tag HTML: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;
                    </p>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article?->is_published))
                            class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Terbitkan sekarang</span>
                    </label>
                    <span class="text-xs text-gray-400">(Jika tidak dicentang, disimpan sebagai draft)</span>
                </div>

            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
                    {{ $article ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn-secondary">Batal</a>
            </div>

        </form>
    </div>

@endsection

@extends('layouts.app')
@section('title', 'Preview Hasil Import')

@section('content')

    <div class="max-w-4xl">

        {{-- Warnings --}}
        @if (!empty($parsed['warnings']))
            <div class="card mb-4 bg-yellow-50 border-yellow-200">
                <div class="flex gap-2 items-start">
                    <span class="text-yellow-500">⚠️</span>
                    <div>
                        <p class="font-medium text-yellow-800 text-sm mb-1">
                            Peringatan
                        </p>
                        @foreach ($parsed['warnings'] as $w)
                            <p class="text-sm text-yellow-700">{{ $w }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-3 gap-6">

            {{-- Konfirmasi form (kiri) --}}
            <div class="col-span-1">
                <div class="card sticky top-20">
                    <h3 class="font-semibold text-gray-800 mb-4">
                        ✏️ Konfirmasi & Edit
                    </h3>

                    <form method="POST" action="{{ route('admin.articles.import.confirm') }}">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Judul</label>
                                <input type="text" name="title" value="{{ $parsed['title'] }}"
                                    class="form-input text-sm">
                            </div>

                            <div>
                                <label class="form-label">Komoditas</label>
                                <select name="commodity" class="form-input text-sm">
                                    @foreach (['cabai', 'kentang', 'jagung', 'umum'] as $c)
                                        <option value="{{ $c }}" @selected($parsed['commodity'] === $c)>
                                            {{ ucfirst($c) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-input text-sm">
                                    @foreach (['budidaya', 'pemupukan', 'pengendalian', 'pascapanen', 'umum'] as $k)
                                        <option value="{{ $k }}" @selected($parsed['category'] === $k)>
                                            {{ ucfirst($k) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div
                                class="flex items-center gap-2 pt-2
                                    border-t border-gray-100">
                                <input type="checkbox" id="is_published" name="is_published" value="1"
                                    class="w-4 h-4 text-primary-600 rounded">
                                <label for="is_published" class="text-sm font-medium text-gray-700">
                                    Terbitkan sekarang
                                </label>
                            </div>

                            <button type="submit" class="btn-primary w-full">
                                ✅ Simpan Artikel
                            </button>
                        </div>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('admin.articles.import') }}"
                            class="btn-secondary w-full text-center block text-sm">
                            ← Upload Ulang
                        </a>
                    </div>

                    {{-- Info section count --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            📊 Hasil parsing:
                        </p>
                        <ul class="mt-2 space-y-1 text-xs text-gray-600">
                            <li>✓ Judul ditemukan</li>
                            <li>✓ Komoditas:
                                <strong>{{ $parsed['commodity'] }}</strong>
                            </li>
                            <li>✓ Kategori:
                                <strong>{{ $parsed['category'] }}</strong>
                            </li>
                            <li>✓ {{ count($parsed['sections']) }} bagian konten</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Preview konten (kanan) --}}
            <div class="col-span-2">
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">
                            👁️ Preview Artikel
                        </h3>
                        <span class="text-xs text-gray-400">
                            Tampilan akan sama seperti di aplikasi mobile
                        </span>
                    </div>

                    {{-- Judul --}}
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $parsed['title'] }}
                    </h1>

                    {{-- Meta badge --}}
                    <div class="flex gap-2 mb-6">
                        <span class="badge-{{ $parsed['commodity'] }}">
                            {{ $parsed['commodity'] }}
                        </span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5
                                 rounded-full text-xs font-medium
                                 bg-gray-100 text-gray-700">
                            {{ $parsed['category'] }}
                        </span>
                    </div>

                    <hr class="mb-6">

                    {{-- Konten per section --}}
                    @foreach ($parsed['sections'] as $section)
                        <h2 class="text-lg font-bold text-primary-800 mt-6 mb-3">
                            {{ $section['heading'] }}
                        </h2>
                        <div class="text-gray-700 text-sm leading-relaxed">
                            @foreach (explode("\n", $section['content']) as $line)
                                @php $line = trim($line); @endphp
                                @if (!empty($line))
                                    @if (str_starts_with($line, '• '))
                                        <li class="ml-4 mb-1">
                                            {{ substr($line, 2) }}
                                        </li>
                                    @else
                                        <p class="mb-2">{{ $line }}</p>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

@endsection

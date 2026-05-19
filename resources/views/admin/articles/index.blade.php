@extends('layouts.app')
@section('title', 'Manajemen Artikel')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div></div>
        <a href="{{ route('admin.articles.create') }}" class="btn-primary flex items-center gap-2">
            <span>+</span> Tulis Artikel Baru
        </a>
    </div>

    {{-- Filter --}}
    <div class="card mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                    placeholder="Judul artikel...">
            </div>
            <div>
                <label class="form-label">Komoditas</label>
                <select name="commodity" class="form-input">
                    <option value="">Semua</option>
                    @foreach (['cabai', 'kentang', 'jagung', 'umum'] as $c)
                        <option value="{{ $c }}" @selected(request('commodity') === $c)>
                            {{ ucfirst($c) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua</option>
                    <option value="published" @selected(request('status') === 'published')>Terbit</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.articles.index') }}" class="btn-secondary">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="table-th">Judul</th>
                    <th class="table-th">Komoditas</th>
                    <th class="table-th">Kategori</th>
                    <th class="table-th">Status</th>
                    <th class="table-th">Penulis</th>
                    <th class="table-th">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="table-td">
                            <p class="font-medium text-gray-900 max-w-xs truncate">{{ $article->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $article->views }} views</p>
                        </td>
                        <td class="table-td">
                            <span class="badge-{{ $article->commodity }}">{{ $article->commodity }}</span>
                        </td>
                        <td class="table-td text-gray-500">{{ $article->category }}</td>
                        <td class="table-td">
                            @if ($article->is_published)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Terbit
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="table-td text-gray-500 text-xs">{{ $article->author->name }}</td>
                        <td class="table-td">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.articles.edit', $article->id) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>

                                <form method="POST" action="{{ route('admin.articles.toggle', $article->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="text-xs font-medium {{ $article->is_published ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                        {{ $article->is_published ? 'Sembunyikan' : 'Terbitkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}"
                                    onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <p class="text-4xl mb-2">📭</p>
                            <p>Belum ada artikel</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

@endsection

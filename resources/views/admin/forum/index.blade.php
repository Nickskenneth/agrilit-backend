@extends('layouts.app')
@section('title', 'Moderasi Forum')

@section('content')

    {{-- Tab filter --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        @foreach ([
            'all' => 'Semua',
            'pending' => 'Menunggu Moderasi',
            'approved' => 'Approved',
            'answered' => 'Terjawab',
        ] as $val => $label)
            <a href="{{ route('admin.forum.index') }}?status={{ $val }}"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                  {{ $status === $val
                      ? 'border-primary-600 text-primary-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
                @if ($val === 'pending' && $pendingCount > 0)
                    <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($posts as $post)
            <div class="card hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-{{ $post->commodity }}">{{ $post->commodity }}</span>
                            @if ($post->status === 'pending')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">⏳
                                    Pending</span>
                            @elseif($post->is_answered)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">✓
                                    Terjawab</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">💬
                                    Belum dijawab</span>
                            @endif
                        </div>

                        <h3 class="font-medium text-gray-900">{{ $post->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $post->content }}</p>

                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                            <span>👤 {{ $post->user->name }}</span>
                            <span>👁 {{ $post->views }} views</span>
                            <span>🕐 {{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <a href="{{ route('admin.forum.show', $post->id) }}"
                            class="btn-secondary text-xs text-center">Lihat Detail</a>

                        @if ($post->status === 'pending')
                            <form method="POST" action="{{ route('admin.forum.moderate', $post->id) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="w-full text-xs py-1.5 px-3 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg font-medium transition-colors">
                                    ✓ Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.forum.moderate', $post->id) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" onclick="return confirm('Tolak post ini?')"
                                    class="w-full text-xs py-1.5 px-3 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg font-medium transition-colors">
                                    ✗ Tolak
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <p class="text-5xl mb-3">💬</p>
                <p class="text-gray-500">Tidak ada post di kategori ini</p>
            </div>
        @endforelse

        {{ $posts->withQueryString()->links() }}
    </div>

@endsection

@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="card flex items-center">
            <div class="text-4xl mr-4">📰</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['articles'] }}</p>
                <p class="text-sm text-gray-500">Artikel Terbit</p>
                <p class="text-xs text-gray-400">{{ $stats['articles_draft'] }} draft</p>
            </div>
        </div>

        <div class="card flex items-center">
            <div class="text-4xl mr-4">💬</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['forum_total'] }}</p>
                <p class="text-sm text-gray-500">Post Forum</p>
                @if ($stats['forum_pending'] > 0)
                    <p class="text-xs text-red-500 font-medium">{{ $stats['forum_pending'] }} menunggu moderasi</p>
                @else
                    <p class="text-xs text-green-500">Semua termoderasi ✓</p>
                @endif
            </div>
        </div>

        <div class="card flex items-center">
            <div class="text-4xl mr-4">🔬</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['scans_total'] }}</p>
                <p class="text-sm text-gray-500">Total Scan AI</p>
                <p class="text-xs text-gray-400">{{ $stats['scans_today'] }} hari ini</p>
            </div>
        </div>

        <div class="card flex items-center">
            <div class="text-4xl mr-4">👨‍🌾</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['users_petani'] }}</p>
                <p class="text-sm text-gray-500">Petani Terdaftar</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Forum Pending --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Forum Menunggu Moderasi</h2>
                <a href="{{ route('admin.forum.index') }}?status=pending"
                    class="text-sm text-primary-600 hover:text-primary-700">Lihat semua →</a>
            </div>

            @forelse($pendingPosts as $post)
                <div class="flex items-start py-3 border-b border-gray-100 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $post->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="badge-{{ $post->commodity }} ml-3 flex-shrink-0">
                        {{ $post->commodity }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-4xl mb-2">✅</p>
                    <p class="text-sm text-gray-500">Tidak ada post yang menunggu moderasi</p>
                </div>
            @endforelse
        </div>

        {{-- Top Diseases --}}
        <div class="card">
            <h2 class="font-semibold text-gray-800 mb-4">Penyakit Paling Banyak Dilaporkan</h2>

            @php $maxCount = $topDiseases->max('total') ?: 1; @endphp

            @forelse($topDiseases as $disease)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $disease->result_label_id }}</span>
                        <span class="font-medium text-gray-900">{{ $disease->total }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full transition-all"
                            style="width: {{ ($disease->total / $maxCount) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada data scan</p>
            @endforelse

            {{-- Scan per komoditas --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex gap-4">
                @foreach (['cabai' => '🌶️', 'kentang' => '🥔', 'jagung' => '🌽'] as $com => $emoji)
                    <div class="flex-1 text-center">
                        <p class="text-lg">{{ $emoji }}</p>
                        <p class="text-xl font-bold text-gray-900">{{ $scanByCommodity[$com] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ $com }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

@endsection

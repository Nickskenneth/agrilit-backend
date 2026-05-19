@extends('layouts.app')
@section('title', 'SOP Budidaya')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.sops.create') }}" class="btn-primary">+ Buat SOP Baru</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($sops as $sop)
            <div class="card hover:shadow-md transition-shadow flex flex-col">

                @if ($sop->thumbnail)
                    <img src="{{ asset('storage/' . $sop->thumbnail) }}" class="w-full h-40 object-cover rounded-lg mb-4"
                        alt="">
                @else
                    <div class="w-full h-40 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                        <span class="text-5xl">
                            {{ ['cabai' => '🌶️', 'kentang' => '🥔', 'jagung' => '🌽'][$sop->commodity] ?? '🌱' }}
                        </span>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-{{ $sop->commodity }}">{{ $sop->commodity }}</span>
                        @if ($sop->is_published)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">
                                ✓ Terbit
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                                Draft
                            </span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-900 mb-1">{{ $sop->title }}</h3>

                    @if ($sop->description)
                        <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ $sop->description }}</p>
                    @endif

                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                        @if ($sop->duration_days)
                            <span>⏱ {{ $sop->duration_days }} hari</span>
                        @endif
                        <span>📅 {{ count($sop->monthly_calendar ?? []) }} aktivitas</span>
                        <span>✍️ {{ $sop->author->name }}</span>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.sops.edit', $sop->id) }}"
                        class="btn-secondary text-xs flex-1 text-center">Edit</a>
                    <form method="POST" action="{{ route('admin.sops.destroy', $sop->id) }}"
                        onsubmit="return confirm('Hapus SOP ini?')" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger text-xs w-full">Hapus</button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-3 text-center py-16">
                <p class="text-5xl mb-3">📋</p>
                <p class="text-gray-500">Belum ada SOP. Buat SOP pertama!</p>
                <a href="{{ route('admin.sops.create') }}" class="btn-primary mt-4 inline-block">
                    + Buat SOP Baru
                </a>
            </div>
        @endforelse
    </div>

@endsection

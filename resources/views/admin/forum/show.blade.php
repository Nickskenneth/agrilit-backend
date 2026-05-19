@extends('layouts.app')
@section('title', 'Detail Forum')

@section('content')

    <div class="max-w-3xl space-y-6">

        {{-- Pertanyaan --}}
        <div class="card">
            <div class="flex items-center gap-2 mb-3">
                <span class="badge-{{ $post->commodity }}">{{ $post->commodity }}</span>
                @if ($post->status === 'pending')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">⏳
                        Pending</span>
                @endif
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $post->title }}</h2>
            <p class="text-gray-700 leading-relaxed">{{ $post->content }}</p>
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="mt-4 rounded-lg max-h-64 object-cover" alt="Foto">
            @endif
            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 text-sm text-gray-500">
                <span>👤 {{ $post->user->name }}</span>
                <span>🕐 {{ $post->created_at->format('d M Y H:i') }}</span>
                <span>👁 {{ $post->views }} views</span>
            </div>

            @if ($post->status === 'pending')
                <div class="flex gap-3 mt-4 pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.forum.moderate', $post->id) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-primary text-sm">✓ Setujui Post</button>
                    </form>
                    <form method="POST" action="{{ route('admin.forum.moderate', $post->id) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" onclick="return confirm('Tolak post ini?')" class="btn-danger text-sm">✗
                            Tolak</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Replies --}}
        @if ($post->replies->count() > 0)
            <div>
                <h3 class="font-semibold text-gray-700 mb-3">
                    {{ $post->replies->count() }} Balasan
                </h3>
                <div class="space-y-3">
                    @foreach ($post->replies as $reply)
                        <div class="card {{ $reply->is_expert_answer ? 'border-l-4 border-primary-500' : '' }}">
                            @if ($reply->is_expert_answer)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-primary-100 text-primary-800 font-medium mb-2">
                                    ⭐ Jawaban Pakar
                                </span>
                            @endif
                            <p class="text-gray-700 whitespace-pre-line">{{ $reply->content }}</p>
                            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                                <span>👤 {{ $reply->user->name }} ({{ $reply->user->role }})</span>
                                <span>🕐 {{ $reply->created_at->format('d M Y H:i') }}</span>
                                <span>👍 {{ $reply->upvotes }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form jawab (hanya jika post approved) --}}
        @if ($post->status === 'approved')
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Tulis Jawaban sebagai Pakar</h3>
                <form method="POST" action="{{ route('admin.forum.reply', $post->id) }}">
                    @csrf
                    <textarea name="content" rows="5" class="form-input mb-3 @error('content') border-red-500 @enderror"
                        placeholder="Tulis jawaban Anda di sini...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn-primary">Kirim Jawaban</button>
                </form>
            </div>
        @endif

        <a href="{{ route('admin.forum.index') }}" class="inline-block text-sm text-primary-600 hover:text-primary-700">
            ← Kembali ke Forum
        </a>

    </div>

@endsection

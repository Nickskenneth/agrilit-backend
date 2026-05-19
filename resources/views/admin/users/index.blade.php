@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @foreach ([['role' => 'admin', 'icon' => '👑', 'label' => 'Admin', 'color' => 'red'], ['role' => 'pakar', 'icon' => '🎓', 'label' => 'Pakar', 'color' => 'blue'], ['role' => 'petani', 'icon' => '👨‍🌾', 'label' => 'Petani', 'color' => 'green']] as $r)
            <div class="card flex items-center gap-3">
                <span class="text-3xl">{{ $r['icon'] }}</span>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $counts[$r['role']] }}</p>
                    <p class="text-sm text-gray-500">{{ $r['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter + Add --}}
    <div class="card mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                    placeholder="Nama atau email...">
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-input">
                    <option value="">Semua</option>
                    @foreach (['admin', 'pakar', 'petani'] as $r)
                        <option value="{{ $r }}" @selected(request('role') === $r)>
                            {{ ucfirst($r) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary ml-auto">+ Tambah User</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="table-th">Nama</th>
                    <th class="table-th">Email</th>
                    <th class="table-th">Role</th>
                    <th class="table-th">Wilayah</th>
                    <th class="table-th">Bergabung</th>
                    <th class="table-th">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 {{ $user->id === auth()->id() ? 'bg-primary-50' : '' }}">
                        <td class="table-td">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary-100 flex items-center
                                        justify-center text-primary-700 font-medium text-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    @if ($user->id === auth()->id())
                                        <p class="text-xs text-primary-600">(Anda)</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="table-td text-gray-500 text-xs">{{ $user->email }}</td>
                        <td class="table-td">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.role', $user->id) }}">
                                    @csrf @method('PATCH')
                                    <select name="role" onchange="this.form.submit()"
                                        class="text-xs border border-gray-200 rounded-lg px-2 py-1
                                               focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        @foreach (['petani', 'pakar', 'admin'] as $r)
                                            <option value="{{ $r }}" @selected($user->role === $r)>
                                                {{ ucfirst($r) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                         font-medium bg-primary-100 text-primary-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @endif
                        </td>
                        <td class="table-td text-gray-500 text-xs">
                            {{ $user->region ?? '-' }}
                        </td>
                        <td class="table-td text-gray-400 text-xs">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="table-td">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                    onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <p class="text-4xl mb-2">👥</p>
                            <p>Tidak ada user ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection

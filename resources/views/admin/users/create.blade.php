@extends('layouts.app')
@section('title', 'Tambah User Baru')

@section('content')

    <div class="max-w-lg">
        <div class="card">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') border-red-500 @enderror" placeholder="Contoh: Dr. Budi Santoso">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') border-red-500 @enderror" placeholder="email@agrilit.id">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-input @error('role') border-red-500 @enderror">
                        <option value="petani" @selected(old('role') === 'petani')>👨‍🌾 Petani</option>
                        <option value="pakar" @selected(old('role') === 'pakar')>🎓 Pakar</option>
                        <option value="admin" @selected(old('role') === 'admin')>👑 Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="form-label">Wilayah</label>
                    <input type="text" name="region" value="{{ old('region') }}" class="form-input"
                        placeholder="Contoh: Pujon, Malang">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Simpan User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

@endsection

<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — AgriLit Admin</title>
    @vite(['resources/css/app.css'])
</head>

<body class="h-full flex items-center justify-center">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🌱</div>
            <h1 class="text-3xl font-bold text-gray-900">AgriLit Admin</h1>
            <p class="text-gray-500 mt-2">Platform Literasi Pertanian</p>
        </div>

        <div class="card">
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') border-red-500 @enderror" placeholder="admin@agrilit.id"
                        required autofocus>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-input @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full justify-center flex py-2.5">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Akses hanya untuk Pakar & Admin AgriLit
        </p>
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — AgriLit Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-full">

    <div class="min-h-full">

        {{-- ===== SIDEBAR ===== --}}
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 flex flex-col">

            {{-- Logo --}}
            <div class="flex h-16 items-center px-6 border-b border-primary-800">
                <span class="text-white font-bold text-xl">🌱 AgriLit</span>
                <span class="ml-2 text-primary-300 text-sm">Admin</span>
            </div>

            {{-- User info --}}
            <div class="px-6 py-4 border-b border-primary-800">
                <p class="text-white text-sm font-medium">{{ Auth::user()->name }}</p>
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                {{ Auth::user()->role === 'admin' ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800' }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">

                <x-nav-item route="admin.dashboard" icon="📊" label="Dashboard" />
                <x-nav-item route="admin.articles.index" icon="📰" label="Artikel" />
                <x-nav-item route="admin.sops.index" icon="📋" label="SOP Budidaya" />
                <x-nav-item route="admin.forum.index" icon="💬" label="Forum">
                    @php
                        $pendingCount = \App\Models\ForumPost::where('status', 'pending')->count();
                    @endphp
                    @if ($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </x-nav-item>
                <x-nav-item route="admin.disease-map.index" icon="🗺️" label="Peta Penyakit" />

                @if (Auth::user()->role === 'admin')
                    <div class="pt-4 pb-2">
                        <p class="text-primary-400 text-xs uppercase font-semibold tracking-wider px-3">Admin</p>
                    </div>
                    <x-nav-item route="admin.users.index" icon="👥" label="Manajemen User" />
                @endif

            </nav>

            {{-- Logout --}}
            <div class="p-4 border-t border-primary-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-3 py-2 text-sm text-primary-300
                           hover:bg-primary-800 hover:text-white rounded-lg transition-colors">
                        <span class="mr-3">🚪</span> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="pl-64">

            {{-- Top bar --}}
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200 h-16 flex items-center px-8">
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                <div class="ml-auto text-sm text-gray-500">
                    {{ now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="px-8 pt-4">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                        <span class="text-green-600 mr-2">✅</span>
                        <p class="text-green-800 text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                        <span class="text-red-600 mr-2">❌</span>
                        <p class="text-red-800 text-sm">{{ session('error') }}</p>
                    </div>
                @endif
            </div>

            {{-- Page content --}}
            <main class="px-8 py-6">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>

</html>

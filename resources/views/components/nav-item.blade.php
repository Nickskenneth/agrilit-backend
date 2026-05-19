@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ route($route) }}"
    class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
          {{ $isActive
              ? 'bg-primary-700 text-white font-medium'
              : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
    <span class="mr-3 text-base">{{ $icon }}</span>
    {{ $label }}
    {{ $slot }}
</a>

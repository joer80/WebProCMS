@props(['name', 'variant' => 'outline'])
@php
    static $icons = null;
    if ($icons === null) {
        $icons = require resource_path('heroicons/data.php');
    }

    $path = $icons[$variant][$name] ?? null;
    $viewBox = match ($variant) {
        'mini'  => '0 0 20 20',
        'micro' => '0 0 16 16',
        default => '0 0 24 24',
    };
@endphp

@if ($path)
    @if ($variant === 'outline')
        <svg {{ $attributes->merge(['xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => $viewBox, 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.5', 'aria-hidden' => 'true']) }}>{!! $path !!}</svg>
    @else
        <svg {{ $attributes->merge(['xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => $viewBox, 'fill' => 'currentColor', 'aria-hidden' => 'true']) }}>{!! $path !!}</svg>
    @endif
@endif

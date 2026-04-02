@props(['name'])
@php
    static $icons = null;
    if ($icons === null) {
        $icons = require resource_path('ionicons/data.php');
    }

    $path = $icons[$name] ?? null;
@endphp

@if ($path)
    <svg {{ $attributes->merge(['xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 512 512', 'aria-hidden' => 'true']) }}>{!! $path !!}</svg>
@endif

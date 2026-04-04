@blaze
@props(['slug', 'prefix' => 'button', 'default' => 'Get Started', 'defaultUrl' => '#', 'defaultClasses' => 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $default);
$url = content($slug, "{$prefix}_url", $defaultUrl);
$newTab = content($slug, "{$prefix}_new_tab", '');
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
<a href="{{ $url }}" @if($newTab) target="_blank" rel="noopener noreferrer" @endif data-editor-group="{{ $prefix }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $label }}</a>
@endif

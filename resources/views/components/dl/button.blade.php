@blaze
@props(['slug', 'prefix' => 'button', 'default' => 'Submit', 'defaultClasses' => 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
<button {{ $attributes->merge(['class' => $cls]) }}>{{ $label }}</button>
@endif

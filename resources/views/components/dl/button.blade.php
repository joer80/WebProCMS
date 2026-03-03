@blaze
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
<button {{ $attributes->merge(['class' => $cls]) }}>{{ $label }}</button>
@endif

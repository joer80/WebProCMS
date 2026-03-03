@blaze
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
<p class="{{ $cls }}">{{ $text }}</p>
@endif

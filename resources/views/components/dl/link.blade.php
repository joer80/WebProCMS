@blaze
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $defaultLabel);
$url = content($slug, "{$prefix}_url", $defaultUrl);
$newTab = content($slug, "{$prefix}_new_tab", '');
$classes = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
<a href="{{ $url }}" class="{{ $classes }}"@if($newTab) target="_blank" rel="noopener noreferrer"@endif>{{ $label }}</a>
@endif

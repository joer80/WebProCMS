@blaze
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$tag = content($slug, "{$prefix}_htag", $defaultTag);
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
{!! "<{$tag} class=\"" . e($cls) . "\">" . e($text) . "</{$tag}>" !!}
@endif

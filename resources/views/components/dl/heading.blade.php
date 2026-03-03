@blaze
@props(['slug', 'prefix' => 'headline', 'default' => '', 'defaultTag' => 'h2', 'defaultClasses' => 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$tag = content($slug, "{$prefix}_htag", $defaultTag);
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
{!! "<{$tag} class=\"" . e($cls) . "\">" . e($text) . "</{$tag}>" !!}
@endif

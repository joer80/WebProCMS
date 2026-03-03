@blaze
@props(['slug', 'prefix' => 'subheadline', 'default' => '', 'defaultClasses' => 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'tag' => 'p'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
@endphp
@if($toggle)
{!! "<{$tag} " . $attributes->merge(['class' => $cls])->toHtml() . ">" . e($text) . "</{$tag}>" !!}
@endif

@blaze
@props(['slug', 'prefix' => 'headline', 'default' => '', 'defaultTag' => 'h2', 'defaultClasses' => 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$tag = content($slug, "{$prefix}_htag", $defaultTag);
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$headingId = content($slug, "{$prefix}_id", '');
$headingAttrs = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = $headingId ? ' id="' . e($headingId) . '"' : '';
foreach ($headingAttrs as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
@endphp
@if($toggle)
{!! "<{$tag}{$extraAttrsStr} class=\"" . e($cls) . "\">" . e($text) . "</{$tag}>" !!}
@endif

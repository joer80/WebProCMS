@blaze
@props(['slug', 'prefix' => 'link', 'defaultLabel' => 'View all →', 'defaultUrl' => '#', 'defaultClasses' => 'text-primary font-semibold hover:text-primary/80 transition-colors text-sm'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $defaultLabel);
$url = content($slug, "{$prefix}_url", $defaultUrl);
$newTab = content($slug, "{$prefix}_new_tab", '');
$classes = content($slug, "{$prefix}_classes", $defaultClasses);
$linkId = content($slug, "{$prefix}_id", '');
$linkAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = $linkId ? ' id="' . e($linkId) . '"' : '';
foreach ($linkAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
@endphp
@if($toggle)
<a href="{{ $url }}" class="{{ $classes }}"@if($newTab) target="_blank" rel="noopener noreferrer"@endif{!! $extraAttrsStr !!}>{{ $label }}</a>
@endif

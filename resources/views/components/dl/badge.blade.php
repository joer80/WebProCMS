@blaze
@props(['slug', 'prefix' => 'badge', 'defaultLabel' => 'Now in Beta', 'defaultClasses' => 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$label = content($slug, $prefix, $defaultLabel);
$classes = content($slug, "{$prefix}_classes", $defaultClasses);
$badgeId = content($slug, "{$prefix}_id", '');
$badgeAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = ' data-editor-group="' . e($prefix) . '"';
$extraAttrsStr .= $badgeId ? ' id="' . e($badgeId) . '"' : '';
foreach ($badgeAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
@endphp
@if($toggle)
<span class="{{ $classes }}"{!! $extraAttrsStr !!}>{{ $label }}</span>
@endif

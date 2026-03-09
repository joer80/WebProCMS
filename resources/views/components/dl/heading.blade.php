@blaze
@props(['slug', 'prefix' => 'headline', 'default' => '', 'defaultTag' => 'h2', 'defaultClasses' => 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$tag = content($slug, "{$prefix}_htag", $defaultTag);
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$headingId = content($slug, "{$prefix}_id", '');
$headingAttrs = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = ' data-editor-group="' . e($prefix) . '"';
$extraAttrsStr .= $headingId ? ' id="' . e($headingId) . '"' : '';
foreach ($headingAttrs as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
$animPreset = content($slug, "{$prefix}_animation", '');
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelay = content($slug, "{$prefix}_animation_delay", '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $extraAttrsStr .= " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
@endphp
@if($toggle)
{!! "<{$tag}{$extraAttrsStr} class=\"" . e($cls) . "\">" . e($text) . "</{$tag}>" !!}
@endif

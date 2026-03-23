@blaze
@props(['slug', 'prefix' => 'subheadline', 'default' => '', 'defaultClasses' => 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'tag' => 'p', 'noToggle' => false])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$subId = content($slug, "{$prefix}_id", '');
$subAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = array_merge(['data-editor-group' => $prefix], $subId ? ['id' => $subId] : []);
foreach ($subAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
$animPreset = content($slug, "{$prefix}_animation", '');
$animAttr = '';
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelay = content($slug, "{$prefix}_animation_delay", '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $animAttr = " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
@endphp
@if($noToggle || $toggle)
@php
$trimmedText = trim($text);
$isMultiParagraph = (bool) preg_match('/<\/p>\s*<p/i', $trimmedText);
if ($isMultiParagraph) {
    $renderedText = $trimmedText;
    $renderTag = 'div';
} else {
    $renderedText = preg_replace('/^<p>(.*)<\/p>$/s', '$1', $trimmedText);
    $renderTag = $tag;
}
@endphp
{!! "<{$renderTag} " . $attributes->merge(array_merge(['class' => $cls], $extraAttrs))->toHtml() . $animAttr . ">" . $renderedText . "</{$renderTag}>" !!}
@endif

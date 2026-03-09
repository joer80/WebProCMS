@blaze
@props(['slug', 'tag' => 'section', 'defaultSectionClasses' => 'py-section px-6 bg-white dark:bg-zinc-900', 'defaultContainerClasses' => 'max-w-6xl mx-auto', 'defaultSticky' => null])
@php
$sectionCls = content($slug, 'section_classes', $defaultSectionClasses);
if ($defaultSticky !== null) {
    $isSticky = content($slug, 'toggle_sticky', $defaultSticky ? '1' : '');
    $sectionCls = ($isSticky ? 'sticky top-0 ' : '') . $sectionCls;
}
$containerCls = content($slug, 'section_container_classes', $defaultContainerClasses);
$sectionId = content($slug, 'section_id', '');
$sectionAttrs = json_decode(content($slug, 'section_attrs', '[]'), true) ?: [];
$extraAttrs = $sectionId ? ['id' => $sectionId] : [];
foreach ($sectionAttrs as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
$animAttr = '';
$animPreset = content($slug, 'section_animation', '');
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelays = \App\View\Components\Dl\Section::animationDelays();
    $animDelay = content($slug, 'section_animation_delay', '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $animAttr = " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
$bgPosition = content($slug, 'section_bg_position', '');
if ($bgPosition) {
    $sectionCls = trim(preg_replace('/\bbg-(?:left-top|left-bottom|right-top|right-bottom|center|top|bottom|left|right)\b/', '', $sectionCls));
    $sectionCls = trim(preg_replace('/\s+/', ' ', $sectionCls . ' bg-' . $bgPosition));
}
$bgSize = content($slug, 'section_bg_size', '');
if ($bgSize) {
    $sectionCls = trim(preg_replace('/\bbg-(?:auto|cover|contain)\b/', '', $sectionCls));
    $sectionCls = trim(preg_replace('/\s+/', ' ', $sectionCls . ' bg-' . $bgSize));
}
$bgRepeat = content($slug, 'section_bg_repeat', '');
if ($bgRepeat) {
    $sectionCls = trim(preg_replace('/\bbg-(?:no-repeat|repeat-x|repeat-y|repeat-round|repeat-space|repeat)\b/', '', $sectionCls));
    $sectionCls = trim(preg_replace('/\s+/', ' ', $sectionCls . ' bg-' . $bgRepeat));
}
$bgImagePath = content($slug, 'section_bg_image', '');
if ($bgImagePath) {
    $extraAttrs['style'] = "background-image: url('{$bgImagePath}')";
}
@endphp
{!! "<{$tag} " . $attributes->merge(array_merge(['class' => $sectionCls], $extraAttrs))->toHtml() . $animAttr . ">" !!}
<div class="{{ $containerCls }}">
    {{ $slot }}
</div>
{!! "</{$tag}>" !!}

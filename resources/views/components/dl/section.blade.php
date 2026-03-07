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
@endphp
{!! "<{$tag} " . $attributes->merge(array_merge(['class' => $sectionCls], $extraAttrs))->toHtml() . ">" !!}
<div class="{{ $containerCls }}">
    {{ $slot }}
</div>
{!! "</{$tag}>" !!}

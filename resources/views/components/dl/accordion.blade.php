@blaze
@props(['slug', 'prefix' => 'faqs', 'defaultWrapperClasses' => 'divide-y divide-zinc-200 dark:divide-zinc-700'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$wrapperCls = content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses);
$accordionId = content($slug, "{$prefix}_id", '');
$accordionAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = $accordionId ? ['id' => $accordionId] : [];
foreach ($accordionAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
@endphp
@if($toggle)
{!! '<div ' . $attributes->merge(array_merge(['class' => $wrapperCls, 'x-data' => '{ open: null }'], $extraAttrs))->toHtml() . '>' !!}
    {{ $slot }}
</div>
@endif

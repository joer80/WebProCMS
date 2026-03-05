@props(['slug', 'prefix' => 'group', 'tag' => 'div', 'defaultClasses' => '', 'defaultFeaturedClasses' => null, 'featured' => false])
@php
$isVoid = in_array(strtolower($tag), ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$activeCls = $cls;
if ($defaultFeaturedClasses !== null) {
    $featuredCls = content($slug, "{$prefix}_featured_classes", $defaultFeaturedClasses);
    $activeCls = $featured ? $featuredCls : $cls;
}
$groupId = content($slug, "{$prefix}_id", '');
$groupAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = $groupId ? ['id' => $groupId] : [];
foreach ($groupAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
@endphp
@if($isVoid)
{!! "<{$tag} " . $attributes->merge(array_merge(['class' => $activeCls], $extraAttrs))->toHtml() . " />" !!}
@else
{!! "<{$tag} " . $attributes->merge(array_merge(['class' => $activeCls], $extraAttrs))->toHtml() . ">" !!}
{{ $slot }}
{!! "</{$tag}>" !!}
@endif

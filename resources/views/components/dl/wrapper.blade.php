@props(['slug', 'prefix' => 'wrapper', 'tag' => 'div', 'defaultClasses' => '', 'defaultFeaturedClasses' => null, 'featured' => false, 'defaultObjectFit' => null])
@php
$isVoid = in_array(strtolower($tag), ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$activeCls = $cls;
if ($defaultFeaturedClasses !== null) {
    $featuredCls = content($slug, "{$prefix}_featured_classes", $defaultFeaturedClasses);
    $activeCls = $featured ? $featuredCls : $cls;
}
if ($defaultObjectFit !== null) {
    $objectFit = content($slug, "{$prefix}_object_fit", $defaultObjectFit);
    $activeCls = trim(preg_replace('/\bobject-\S+/', '', $activeCls) . ' object-' . $objectFit);
}
$wrapperId = content($slug, "{$prefix}_id", '');
$wrapperAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = array_merge(['data-editor-group' => $prefix], $wrapperId ? ['id' => $wrapperId] : []);
foreach ($wrapperAttrsRaw as $attr) {
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

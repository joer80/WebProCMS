@blaze
@props(['slug', 'prefix' => 'icon', 'name' => 'bolt', 'defaultWrapperClasses' => '', 'defaultClasses' => 'size-8', 'defaultFeaturedClasses' => null, 'featured' => false])
@php
$wrapperCls = $defaultWrapperClasses !== '' ? content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses) : null;
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$activeCls = $cls;
if ($defaultFeaturedClasses !== null) {
    $featuredCls = content($slug, "{$prefix}_featured_classes", $defaultFeaturedClasses);
    $activeCls = $featured ? $featuredCls : $cls;
}
$isIonicon = str_starts_with($name, 'ion:');
if ($isIonicon) {
    $iconName = substr($name, 4);
    $iconVariant = null;
} else {
    [$iconName, $iconVariant] = array_pad(explode(':', $name, 2), 2, 'outline');
}
@endphp
@if($wrapperCls !== null)
<div class="{{ $wrapperCls }}" data-editor-group="{{ $prefix }}">
@endif
@if($isIonicon)
<x-ionicon name="{{ $iconName }}" class="{{ $activeCls }}" />
@else
<x-heroicon name="{{ $iconName }}" variant="{{ $iconVariant }}" class="{{ $activeCls }}" />
@endif
@if($wrapperCls !== null)
</div>
@endif

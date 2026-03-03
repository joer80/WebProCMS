@blaze
@php
$wrapperCls = $defaultWrapperClasses !== '' ? content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses) : null;
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$activeCls = $cls;
if ($defaultFeaturedClasses !== null) {
    $featuredCls = content($slug, "{$prefix}_featured_classes", $defaultFeaturedClasses);
    $activeCls = $featured ? $featuredCls : $cls;
}
[$iconName, $iconVariant] = array_pad(explode(':', $name, 2), 2, 'outline');
@endphp
@if($wrapperCls !== null)
<div class="{{ $wrapperCls }}">
@endif
<x-heroicon name="{{ $iconName }}" variant="{{ $iconVariant }}" class="{{ $activeCls }}" />
@if($wrapperCls !== null)
</div>
@endif

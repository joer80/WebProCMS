@blaze
@props(['slug', 'prefix' => 'image', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultImageClasses' => 'w-full h-full object-cover'])
@php
$wrapperCls = content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses);
$imageCls = content($slug, "{$prefix}_image_classes", $defaultImageClasses);
$imgSrc = content($slug, "{$prefix}_image", '');
$imgAlt = content($slug, "{$prefix}_image_alt", '');
@endphp
@if(content($slug, "toggle_{$prefix}", '1'))
<div class="{{ $wrapperCls }}" data-editor-group="{{ $prefix }}">
    @if($imgSrc)
        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" class="{{ $imageCls }}">
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image</span>
    @endif
</div>
@endif

@blaze
@props(['slug', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultImageClasses' => 'w-full h-full object-cover'])
@php
$wrapperCls = content($slug, 'image_wrapper_classes', $defaultWrapperClasses);
$imageCls = content($slug, 'image_classes', $defaultImageClasses);
$imgSrc = content($slug, 'image', '');
$imgAlt = content($slug, 'image_alt', '');
@endphp
@if(content($slug, 'toggle_image', '1'))
<div class="{{ $wrapperCls }}">
    @if($imgSrc)
        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" class="{{ $imageCls }}">
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
    @endif
</div>
@endif

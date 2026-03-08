@blaze
@props(['slug', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultImageClasses' => 'w-full h-full object-cover', 'defaultImage' => ''])
@php
$wrapperCls = content($slug, 'image_wrapper_classes', $defaultWrapperClasses);
$imageCls = content($slug, 'image_classes', $defaultImageClasses);
$imgSrc = content($slug, 'image', $defaultImage);
$imgAlt = content($slug, 'image_alt', '');
$imgLazy = content($slug, 'toggle_image_lazy', '');
@endphp
@if(content($slug, 'toggle_image', '1'))
<div class="{{ $wrapperCls }}" data-editor-group="media">
    @if($imgSrc)
        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" class="{{ $imageCls }}"@if($imgLazy) loading="lazy"@endif>
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
    @endif
</div>
@endif

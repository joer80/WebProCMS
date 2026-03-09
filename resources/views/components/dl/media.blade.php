@blaze
@props(['slug', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultImageClasses' => 'w-full h-full object-cover', 'defaultImage' => ''])
@php
$wrapperCls = content($slug, 'image_wrapper_classes', $defaultWrapperClasses);
$imageCls = content($slug, 'image_classes', $defaultImageClasses);
$objectFit = content($slug, 'image_object_fit', '');
if ($objectFit) {
    $imageCls = trim(preg_replace('/\bobject-\S+/', '', $imageCls) . ' object-' . $objectFit);
}
$borderRadius = content($slug, 'image_border_radius', '');
if ($borderRadius) {
    $wrapperCls = preg_replace('/\s+/', ' ', trim(preg_replace('/\brounded(-\w+)?\b/', '', $wrapperCls) . ' ' . $borderRadius));
}
$imgSrc = content($slug, 'image', $defaultImage);
$imgAlt = content($slug, 'image_alt', '');
$imgLazy = content($slug, 'toggle_image_lazy', '');
$animPreset = content($slug, 'image_animation', '');
$animAttr = '';
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelay = content($slug, 'image_animation_delay', '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $animAttr = " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
@endphp
@if(content($slug, 'toggle_image', '1'))
{!! "<div class=\"{$wrapperCls}\" data-editor-group=\"media\"{$animAttr}>" !!}
    @if($imgSrc)
        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" class="{{ $imageCls }}"@if($imgLazy) loading="lazy"@endif>
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
    @endif
</div>
@endif

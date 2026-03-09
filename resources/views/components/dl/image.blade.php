@blaze
@props(['slug', 'prefix' => 'image', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultImageClasses' => 'w-full h-full object-cover', 'defaultImage' => ''])
@php
$wrapperCls = content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses);
$imageCls = content($slug, "{$prefix}_image_classes", $defaultImageClasses);
$objectFit = content($slug, "{$prefix}_object_fit", '');
if ($objectFit) {
    $imageCls = trim(preg_replace('/\bobject-\S+/', '', $imageCls) . ' object-' . $objectFit);
}
$imgSrc = content($slug, "{$prefix}_image", $defaultImage);
$imgAlt = content($slug, "{$prefix}_image_alt", '');
$animPreset = content($slug, "{$prefix}_animation", '');
$animAttr = '';
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelay = content($slug, "{$prefix}_animation_delay", '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $animAttr = " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
@endphp
@if(content($slug, "toggle_{$prefix}", '1'))
{!! "<div class=\"{$wrapperCls}\" data-editor-group=\"{$prefix}\"{$animAttr}>" !!}
    @if($imgSrc)
        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" class="{{ $imageCls }}">
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image</span>
    @endif
</div>
@endif

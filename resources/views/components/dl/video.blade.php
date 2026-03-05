@blaze
@props(['slug', 'prefix' => 'video', 'defaultWrapperClasses' => 'rounded-card overflow-hidden aspect-video', 'defaultVideoClasses' => 'w-full h-full', 'defaultVideoUrl' => ''])
@php
$wrapperCls = content($slug, "{$prefix}_wrapper_classes", $defaultWrapperClasses);
$videoCls = content($slug, "{$prefix}_video_classes", $defaultVideoClasses);
$rawUrl = content($slug, "{$prefix}_video_url", $defaultVideoUrl);
$embedUrl = $rawUrl ? \App\View\Components\Dl\Video::parseEmbedUrl($rawUrl) : '';
@endphp
@if(content($slug, "toggle_{$prefix}", '1'))
<div class="{{ $wrapperCls }}" data-editor-group="{{ $prefix }}">
    @if($embedUrl)
        <iframe src="{{ $embedUrl }}" class="{{ $videoCls }}" frameborder="0" allowfullscreen></iframe>
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Video embed</span>
    @endif
</div>
@endif

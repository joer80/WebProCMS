@blaze
@props(['slug', 'prefix' => 'gallery', 'defaultGridClasses' => 'grid grid-cols-2 md:grid-cols-3 gap-4'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$gridCls = content($slug, "{$prefix}_grid_classes", $defaultGridClasses);
@endphp
@if($toggle)
<div class="{{ $gridCls }}">
    {{ $slot }}
</div>
@endif

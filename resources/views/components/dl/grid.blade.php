@props(['slug', 'prefix' => 'items', 'defaultGridClasses' => 'grid md:grid-cols-3 gap-8'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$gridCls = content($slug, "{$prefix}_grid_classes", $defaultGridClasses);
@endphp
@if($toggle)
<div class="{{ $gridCls }}" data-editor-group="{{ $prefix }}">
    {{ $slot }}
</div>
@endif

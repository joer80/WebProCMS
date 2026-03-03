@blaze
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$gridCls = content($slug, "{$prefix}_grid_classes", $defaultGridClasses);
@endphp
@if($toggle)
<div class="{{ $gridCls }}">
    {{ $slot }}
</div>
@endif

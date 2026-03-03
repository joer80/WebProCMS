@blaze
@php
$sectionCls = content($slug, 'section_classes', $defaultSectionClasses);
$containerCls = content($slug, 'section_container_classes', $defaultContainerClasses);
@endphp
{!! "<{$tag} " . $attributes->merge(['class' => $sectionCls])->toHtml() . ">" !!}
<div class="{{ $containerCls }}">
    {{ $slot }}
</div>
{!! "</{$tag}>" !!}

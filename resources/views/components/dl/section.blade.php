@blaze
@props(['slug', 'tag' => 'section', 'defaultSectionClasses' => 'py-section px-6 bg-white dark:bg-zinc-900', 'defaultContainerClasses' => 'max-w-6xl mx-auto'])
@php
$sectionCls = content($slug, 'section_classes', $defaultSectionClasses);
$containerCls = content($slug, 'section_container_classes', $defaultContainerClasses);
@endphp
{!! "<{$tag} " . $attributes->merge(['class' => $sectionCls])->toHtml() . ">" !!}
<div class="{{ $containerCls }}">
    {{ $slot }}
</div>
{!! "</{$tag}>" !!}

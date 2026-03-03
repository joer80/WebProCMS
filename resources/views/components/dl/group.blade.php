@blaze
@props(['slug', 'prefix' => 'group', 'tag' => 'div', 'defaultClasses' => '', 'defaultFeaturedClasses' => null, 'featured' => false])
@php
$isVoid = in_array(strtolower($tag), ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$activeCls = $cls;
if ($defaultFeaturedClasses !== null) {
    $featuredCls = content($slug, "{$prefix}_featured_classes", $defaultFeaturedClasses);
    $activeCls = $featured ? $featuredCls : $cls;
}
@endphp
@if($isVoid)
{!! "<{$tag} " . $attributes->merge(['class' => $activeCls])->toHtml() . " />" !!}
@else
{!! "<{$tag} " . $attributes->merge(['class' => $activeCls])->toHtml() . ">" !!}
{{ $slot }}
{!! "</{$tag}>" !!}
@endif

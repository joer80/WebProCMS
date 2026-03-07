@blaze
@props(['slug', 'prefix' => 'logo', 'defaultClasses' => 'h-8 w-auto'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$customImage = content($slug, "{$prefix}_image", '');
$imageAlt = content($slug, "{$prefix}_image_alt", '') ?: config('app.name') . ' Logo';
$imgClasses = content($slug, "{$prefix}_classes", $defaultClasses);
$logoId = content($slug, "{$prefix}_id", '');
$logoAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = ' data-editor-group="' . e($prefix) . '"';
$extraAttrsStr .= $logoId ? ' id="' . e($logoId) . '"' : '';
foreach ($logoAttrsRaw as $attr) {
    if (! empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
// Custom image (from media library) takes precedence; falls back to branding config logo.
$logoSrc = $customImage ?: config('branding.logo_url');
@endphp
@if($toggle)
<a href="{{ route('home') }}"{!! $extraAttrsStr !!}>
    @if($logoSrc)
        <img src="{{ $logoSrc }}" alt="{{ $imageAlt }}" class="{{ $imgClasses }}">
    @else
        <span class="{{ $imgClasses }}">{{ $imageAlt }}</span>
    @endif
</a>
@endif

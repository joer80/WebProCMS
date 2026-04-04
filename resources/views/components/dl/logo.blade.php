@blaze
@props(['slug', 'prefix' => 'logo', 'defaultClasses' => 'h-8 w-auto', 'defaultDarkBg' => ''])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$useDarkLogo = content($slug, "toggle_{$prefix}_dark_bg", $defaultDarkBg);
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
// Custom image takes precedence. Otherwise fall back to the dark or light branding logo, then CMS default.
if ($useDarkLogo) {
    $brandingLogo = \App\Models\Setting::get('branding.dark_logo_url') ?: asset('images/logo-dark.svg');
} else {
    $brandingLogo = \App\Models\Setting::get('branding.logo_url') ?: asset('images/logo.svg');
}
$logoSrc = $customImage ?: $brandingLogo;
@endphp
@if($toggle)
<a href="{{ route('home') }}"{!! $extraAttrsStr !!}>
    <img src="{{ $logoSrc }}" alt="{{ $imageAlt }}" class="{{ $imgClasses }}">
</a>
@endif

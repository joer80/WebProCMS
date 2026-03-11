<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="generator" content="WebProCMS" />

<title>{{ $title ?? config('app.name') }}</title>

@if (! empty($description))
    <meta name="description" content="{{ $description }}" />
@endif

@if (! empty($noindex))
    <meta name="robots" content="noindex">
@endif

@if (! empty($ogImage))
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
@endif

<meta property="og:title" content="{{ $title ?? config('app.name') }}" />
@if (! empty($description))
    <meta property="og:description" content="{{ $description }}" />
@endif

<meta name="twitter:title" content="{{ $title ?? config('app.name') }}" />
@if (! empty($description))
    <meta name="twitter:description" content="{{ $description }}" />
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@php
    $fontsToLoad = collect([\App\Models\Setting::get('branding.body_font', 'instrument-sans'), \App\Models\Setting::get('branding.heading_font', 'instrument-sans')])
        ->filter(fn ($f) => $f !== 'system')
        ->unique()
        ->values();
    $bunnyFamilies = ['instrument-sans' => 'instrument-sans:400,500,600', 'inter' => 'inter:400,500,600'];
@endphp
@if ($fontsToLoad->isNotEmpty())
<link rel="preconnect" href="https://fonts.bunny.net">
@foreach ($fontsToLoad as $loadFont)
<link href="https://fonts.bunny.net/css?family={{ $bunnyFamilies[$loadFont] ?? $loadFont }}&display=swap" rel="stylesheet">
@endforeach
@endif

@if (($cssBundle ?? null) === 'resources/css/public.css' && request()->routeIs('design-library.preview'))
    @include('partials.tailwind-cdn-preview')
    @vite(['resources/js/app.js'])
@else
    @vite([$cssBundle ?? 'resources/css/app.css', 'resources/js/app.js'])
@endif
@fluxAppearance
<script>localStorage.setItem('flux.appearance','light');document.documentElement.classList.remove('dark')</script>
@stack('head')

@php
    $schema = \App\Models\Setting::get('seo.schema', []);
    $schemaData = ['@context' => 'https://schema.org', '@type' => $schema['type'] ?? 'Organization'];

    if (! empty($schema['name'])) {
        $schemaData['name'] = $schema['name'];
    }
    if (! empty($schema['url'])) {
        $schemaData['url'] = $schema['url'];
    }
    if (! empty($schema['description'])) {
        $schemaData['description'] = $schema['description'];
    }
    if (! empty($schema['logo'])) {
        $schemaData['logo'] = ['@type' => 'ImageObject', 'url' => $schema['logo']];
    }
    if (! empty($schema['phone'])) {
        $schemaData['telephone'] = $schema['phone'];
    }
    if (! empty($schema['email'])) {
        $schemaData['email'] = $schema['email'];
    }

    $address = $schema['address'];
    if (! empty($address['street']) || ! empty($address['city'])) {
        $postalAddress = ['@type' => 'PostalAddress'];
        if (! empty($address['street'])) {
            $postalAddress['streetAddress'] = $address['street'];
        }
        if (! empty($address['city'])) {
            $postalAddress['addressLocality'] = $address['city'];
        }
        if (! empty($address['region'])) {
            $postalAddress['addressRegion'] = $address['region'];
        }
        if (! empty($address['postal_code'])) {
            $postalAddress['postalCode'] = $address['postal_code'];
        }
        if (! empty($address['country'])) {
            $postalAddress['addressCountry'] = $address['country'];
        }
        $schemaData['address'] = $postalAddress;
    }

    if ($schema['type'] !== 'Organization' && ! empty($schema['hours'])) {
        $schemaData['openingHours'] = $schema['hours'];
    }
@endphp
<script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

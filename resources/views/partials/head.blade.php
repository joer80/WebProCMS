<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

@if (! empty($description))
    <meta name="description" content="{{ $description }}" />
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
<script>localStorage.setItem('flux.appearance','light');document.documentElement.classList.remove('dark')</script>
@stack('head')

@php
    $schema = config('seo.schema');
    $schemaData = ['@context' => 'https://schema.org', '@type' => $schema['type']];

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

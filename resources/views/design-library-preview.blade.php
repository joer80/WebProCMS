<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Preview: {{ $name }}</title>
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
    @vite(['resources/css/public.css', 'resources/css/app.css', 'resources/js/app.js'])
    <style>{!! app(\App\Services\BrandingStyleService::class)->styleBlock() !!}</style>
</head>
<body class="bg-white text-zinc-900 antialiased">
    {!! $content !!}
</body>
</html>

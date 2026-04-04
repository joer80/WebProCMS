@props([
    'sidebar' => false,
])

@php
    $whiteLabelEnabled = \App\Models\Setting::get('branding.white_label', false);
    $customLogoUrl = \App\Models\Setting::get('branding.logo_url');
    $customDarkLogoUrl = \App\Models\Setting::get('branding.dark_logo_url');
    $logoSrc = ($whiteLabelEnabled && $customLogoUrl) ? $customLogoUrl : asset('images/logo.svg');
    $darkLogoSrc = $whiteLabelEnabled ? ($customDarkLogoUrl ?: asset('images/logo-dark.svg')) : asset('images/logo-dark.svg');
@endphp

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto {{ $darkLogoSrc ? 'dark:hidden' : '' }}" />
            @if ($darkLogoSrc)
                <img src="{{ $darkLogoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto hidden dark:block" />
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto {{ $darkLogoSrc ? 'dark:hidden' : '' }}" />
            @if ($darkLogoSrc)
                <img src="{{ $darkLogoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto hidden dark:block" />
            @endif
        </x-slot>
    </flux:brand>
@endif

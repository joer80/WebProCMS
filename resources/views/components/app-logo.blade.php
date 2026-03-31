@props([
    'sidebar' => false,
])

@php
    $whiteLabelEnabled = \App\Models\Setting::get('branding.white_label', false);
    $customLogoUrl = \App\Models\Setting::get('branding.logo_url');
    $logoSrc = ($whiteLabelEnabled && $customLogoUrl) ? $customLogoUrl : asset('images/logo.svg');
@endphp

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
        </x-slot>
    </flux:brand>
@endif

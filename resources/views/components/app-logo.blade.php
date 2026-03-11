@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ \App\Models\Setting::get('branding.logo_url', asset('images/logo.svg')) }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ \App\Models\Setting::get('branding.logo_url', asset('images/logo.svg')) }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
        </x-slot>
    </flux:brand>
@endif

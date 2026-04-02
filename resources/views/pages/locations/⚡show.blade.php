{{-- @previewContext model=\App\Models\Location label=name value=id routeParam=location orderBy=name --}}
<?php

use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    public Location $location;

    public function mount(Location $location): void
    {
        $this->location = $location;
    }

    public function title(): string
    {
        return $this->location->name . ' — ' . config('app.name');
    }
}; ?>
<div>{{-- ROW:start:page-title-banner:LcSHw3 --}}
<x-dl.section slug="page-title-banner:LcSHw3"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="page-title-banner:LcSHw3" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-black/50" />
    <x-dl.heading slug="page-title-banner:LcSHw3" prefix="headline" default="{{ $location->name ?? 'Location' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>
{{-- ROW:end:page-title-banner:LcSHw3 --}}

{{-- ROW:start:location-detail:Kp7mNq --}}
<x-dl.section slug="location-detail:Kp7mNq"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">

    <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="breadcrumb" tag="nav"
        default-classes="mb-8 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('locations') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">Locations</a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-zinc-100">{{ $location->name ?? 'Location' }}</span>
    </x-dl.wrapper>

    <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="content_grid"
        default-classes="grid md:grid-cols-2 gap-10 items-start">

        @if (isset($location) && $location->photoUrl())
            <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="photo" tag="img"
                src="{{ $location->photoUrl() }}"
                alt="{{ $location->name }}"
                default-classes="w-full rounded-card object-cover aspect-video" />
        @else
            <div class="flex aspect-video items-center justify-center rounded-card bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-12 text-zinc-300 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
            </div>
        @endif

        <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="details"
            default-classes="space-y-5">
            <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="location_name" tag="h2"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white">
                {{ $location->name }}
            </x-dl.wrapper>
            <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="address"
                default-classes="space-y-1 text-zinc-600 dark:text-zinc-400">
                <p>{{ $location->address }}</p>
                <p>{{ $location->city }}, {{ $location->state }} {{ $location->zip }}</p>
            </x-dl.wrapper>
            @if (isset($location) && $location->phone)
                <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="phone" tag="p"
                    default-classes="text-zinc-600 dark:text-zinc-400">
                    {{ $location->phone }}
                </x-dl.wrapper>
            @endif
            <x-dl.wrapper slug="location-detail:Kp7mNq" prefix="directions_link" tag="a"
                href="https://maps.google.com/?q={{ urlencode($location->address.', '.$location->city.', '.$location->state.' '.$location->zip) }}"
                target="_blank" rel="noopener noreferrer"
                default-classes="inline-flex px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Get Directions
            </x-dl.wrapper>
        </x-dl.wrapper>

    </x-dl.wrapper>

</x-dl.section>
{{-- ROW:end:location-detail:Kp7mNq --}}
</div>

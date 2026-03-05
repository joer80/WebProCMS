<?php

use App\Models\Location;
use App\Support\States;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Find a GetRows office near you. We have locations across the South and Midwest including Dallas, Houston, Little Rock, Fayetteville, and Oklahoma City.'])] #[Title('Locations — GetRows')] class extends Component {
    public string $selectedState = '';

    /** @return \Illuminate\Database\Eloquent\Collection<int, Location> */
    public function getAllLocationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Location::query()->orderBy('name')->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Location> */
    public function getFilteredLocationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Location::query()
            ->when($this->selectedState !== '', fn ($q) => $q->where('state', $this->selectedState))
            ->orderBy('name')
            ->get();
    }

    /** @return list<string> */
    public function getAvailableStatesProperty(): array
    {
        return Location::query()
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->all();
    }

    public function stateFullName(string $abbreviation): string
    {
        return States::fullName($abbreviation);
    }

    public function filterByState(string $state): void
    {
        $this->selectedState = $state;
    }

    public function clearFilter(): void
    {
        $this->selectedState = '';
    }
    // ROW:php:start:locations-grid:SfKKnK
    public string $locationsSelectedState = '';
    
    #[\Livewire\Attributes\Computed]
    public function locationsFiltered(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Location::query()
            ->when($this->locationsSelectedState !== '', fn ($q) => $q->where('state', $this->locationsSelectedState))
            ->orderBy('name')
            ->get();
    }
    
    #[\Livewire\Attributes\Computed]
    public function locationsAvailableStates(): array
    {
        return \App\Models\Location::query()
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->all();
    }
    
    public function filterLocationsByState(string $state): void
    {
        $this->locationsSelectedState = $state;
    }
    
    public function clearLocationsFilter(): void
    {
        $this->locationsSelectedState = '';
    }
    // ROW:php:end:locations-grid:SfKKnK
}; ?>
<div>{{-- ROW:start:locations-grid:SfKKnK --}}
<x-dl.section slug="locations-grid:SfKKnK"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="header_wrapper" default-classes="mb-10">
        <x-dl.heading slug="locations-grid:SfKKnK" prefix="headline" default="Our Locations"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="locations-grid:SfKKnK" prefix="subheadline" default="Find a location near you."
            default-classes="text-zinc-500 dark:text-zinc-400 leading-normal" />
    </x-dl.wrapper>

    <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="filter_wrapper" default-classes="flex flex-wrap items-center gap-2 mb-8">
        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="filter_button" tag="button"
            wire:click="clearLocationsFilter"
            x-bind:class="$wire.locationsSelectedState === '' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 hover:border-zinc-400'"
            default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
            All
        </x-dl.wrapper>
        @foreach ($this->locationsAvailableStates as $locState)
            <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="filter_button" tag="button"
                wire:click="filterLocationsByState('{{ $locState }}')"
                x-bind:class="$wire.locationsSelectedState === '{{ $locState }}' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 hover:border-zinc-400'"
                default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                {{ \App\Support\States::fullName($locState) }}
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>

    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="locations_grid" default-classes="grid sm:grid-cols-3 gap-6">
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="locations-grid:SfKKnK" prefix="location_card"
                    default-classes="bg-white dark:bg-zinc-800 rounded-card shadow-card overflow-hidden">
                    @if ($location->photoUrl())
                        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="location_image" tag="img"
                            src="{{ $location->photoUrl() }}"
                            alt="{{ $location->name }}"
                            default-classes="w-full h-48 object-cover" />
                    @endif
                    <x-dl.group slug="locations-grid:SfKKnK" prefix="card_content" default-classes="p-5">
                        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="location_name" tag="h3"
                            default-classes="font-semibold text-base text-zinc-900 dark:text-white mb-3">
                            {{ $location->name }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="location_details"
                            default-classes="space-y-1.5 text-sm text-zinc-500 dark:text-zinc-400 mb-5">
                            <p>{{ $location->address }}</p>
                            <p>{{ $location->city }}, {{ $location->state }} {{ $location->zip }}</p>
                            <p>{{ $location->phone }}</p>
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="directions_link" tag="a"
                            href="https://maps.google.com/?q={{ urlencode($location->address.', '.$location->city.', '.$location->state.' '.$location->zip) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            default-classes="inline-block px-4 py-1.5 text-sm border border-zinc-300 dark:border-zinc-600 hover:border-zinc-400 text-zinc-800 dark:text-zinc-200 rounded-sm transition-all">
                            Get Directions
                        </x-dl.wrapper>
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="locations-grid:SfKKnK" prefix="empty_state" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No locations found for the selected state.
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{-- ROW:end:locations-grid:SfKKnK --}}
</div>

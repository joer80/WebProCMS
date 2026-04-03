{{--
@name Locations - Grid
@description Three-column grid of database locations with optional state filter tabs.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find a location near you."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    <x-dl.wrapper slug="__SLUG__" prefix="filter_wrapper" default-classes="flex flex-wrap items-center gap-2 mb-8">
        <x-dl.wrapper slug="__SLUG__" prefix="filter_button" tag="button"
            wire:click="clearLocationsFilter"
            x-bind:class="$wire.locationsSelectedState === '' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 hover:border-zinc-400'"
            default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
            All
        </x-dl.wrapper>
        @foreach ($this->locationsAvailableStates as $locState)
            <x-dl.wrapper slug="__SLUG__" prefix="filter_button" tag="button"
                wire:click="filterLocationsByState('{{ $locState }}')"
                x-bind:class="$wire.locationsSelectedState === '{{ $locState }}' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 hover:border-zinc-400'"
                default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                {{ \App\Support\States::fullName($locState) }}
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>

    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_grid" default-classes="grid sm:grid-cols-3 gap-6">
            @foreach ($this->locationsFiltered as $location)
                <div wire:key="location-{{ $location->id }}" wire:transition>
                <x-dl.card slug="__SLUG__" prefix="location_card"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="bg-white dark:bg-zinc-800 rounded-card border border-zinc-200 dark:border-zinc-700 shadow-card overflow-hidden h-full">
                    @if ($location->photoUrl())
                        <x-dl.wrapper slug="__SLUG__" prefix="location_image" tag="img"
                            src="{{ $location->photoUrl() }}"
                            alt="{{ $location->name }}"
                            default-classes="w-full h-48 object-cover" />
                    @else
                        <div class="w-full h-48 bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                            <svg class="size-10 text-zinc-300 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        </div>
                    @endif
                    <x-dl.group slug="__SLUG__" prefix="card_content" default-classes="p-5">
                        <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                            default-classes="font-semibold text-base text-zinc-900 dark:text-white mb-3">
                            {{ $location->name }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="location_details"
                            default-classes="space-y-1.5 text-sm text-zinc-500 dark:text-zinc-400 mb-5">
                            <p>{{ $location->address }}</p>
                            <p>{{ $location->city }}, {{ $location->state }} {{ $location->zip }}</p>
                            <p>{{ $location->phone }}</p>
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="directions_link" tag="a"
                            href="https://maps.google.com/?q={{ urlencode($location->address.', '.$location->city.', '.$location->state.' '.$location->zip) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            default-classes="inline-block px-4 py-1.5 text-sm border border-zinc-300 dark:border-zinc-600 hover:border-zinc-400 text-zinc-800 dark:text-zinc-200 rounded-sm transition-all">
                            Get Directions
                        </x-dl.wrapper>
                    </x-dl.group>
                </x-dl.card>
                </div>
            @endforeach
        </x-dl.wrapper>
    @else
        <x-dl.subheadline slug="__SLUG__" prefix="empty_state" no-toggle
            default="No locations found for the selected state."
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm" />
    @endif
</x-dl.section>
{{--
@php
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
--}}

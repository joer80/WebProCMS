{{--
@name Locations - Dark
@description Dark background location grid with state filter.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find us near you."
        default-classes="text-zinc-400 mb-8" />
    <x-dl.wrapper slug="__SLUG__" prefix="filter_wrapper"
        default-classes="flex flex-wrap gap-2 mb-10">
        <x-dl.wrapper slug="__SLUG__" prefix="filter_all" tag="button"
            wire:click="clearLocationsFilter"
            x-bind:class="$wire.locationsSelectedState === '' ? 'bg-white text-zinc-900' : 'border border-zinc-700 text-zinc-300 hover:border-zinc-500'"
            default-classes="px-4 py-1.5 text-sm rounded-full transition-all">
            All
        </x-dl.wrapper>
        @foreach ($this->locationsAvailableStates as $locState)
            <x-dl.wrapper slug="__SLUG__" prefix="filter_state" tag="button"
                wire:click="filterLocationsByState('{{ $locState }}')"
                x-bind:class="$wire.locationsSelectedState === '{{ $locState }}' ? 'bg-white text-zinc-900' : 'border border-zinc-700 text-zinc-300 hover:border-zinc-500'"
                default-classes="px-4 py-1.5 text-sm rounded-full transition-all">
                {{ \App\Support\States::fullName($locState) }}
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_grid"
            default-classes="grid sm:grid-cols-2 md:grid-cols-3 gap-6"
            note="Content is pulled from the <a href='/dashboard/locations' class='text-primary underline hover:text-primary/80'>Locations</a> page.">
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_card"
                    default-classes="rounded-card bg-zinc-800 border border-zinc-700 p-6 hover:border-zinc-500 transition-colors">
                    <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                        default-classes="text-base font-semibold text-white mb-2">
                        {{ $location->name }}
                    </x-dl.wrapper>
                    @if ($location->address)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="p"
                            default-classes="text-sm text-zinc-400 mb-2">
                            {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                        </x-dl.wrapper>
                    @endif
                    @if ($location->phone)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_phone" tag="a"
                            href="tel:{{ $location->phone }}"
                            default-classes="text-sm text-primary hover:text-primary/80 transition-colors">
                            {{ $location->phone }}
                        </x-dl.wrapper>
                    @endif
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
use App\Models\Location;
use Livewire\Attributes\Computed;

#[Computed]
public function locationsFiltered(): \Illuminate\Database\Eloquent\Collection
{
    return Location::query()
        ->when($this->locationsSelectedState, fn($q, $s) => $q->where('state', $s))
        ->orderBy('name')
        ->get();
}

#[Computed]
public function locationsAvailableStates(): array
{
    return Location::query()->whereNotNull('state')->distinct()->orderBy('state')->pluck('state')->toArray();
}
--}}

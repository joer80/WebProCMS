{{--
@name Locations - Two Column
@description Heading and filter left, location list right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-3 gap-12">
        <x-dl.wrapper slug="__SLUG__" prefix="left_panel"
            default-classes="md:col-span-1">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Find a Location"
                default-tag="h2"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-4" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Filter by state to find your nearest location."
                default-classes="text-zinc-500 dark:text-zinc-400 mb-6" />
            <x-dl.wrapper slug="__SLUG__" prefix="filter_group"
                default-classes="space-y-2">
                <x-dl.wrapper slug="__SLUG__" prefix="filter_all" tag="button"
                    wire:click="clearLocationsFilter"
                    x-bind:class="$wire.locationsSelectedState === '' ? 'bg-primary text-white border-transparent' : 'border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-primary hover:text-primary'"
                    default-classes="w-full text-left px-4 py-2 text-sm border rounded-lg transition-all">
                    All States
                </x-dl.wrapper>
                @foreach ($this->locationsAvailableStates as $locState)
                    <x-dl.wrapper slug="__SLUG__" prefix="filter_state" tag="button"
                        wire:click="filterLocationsByState('{{ $locState }}')"
                        x-bind:class="$wire.locationsSelectedState === '{{ $locState }}' ? 'bg-primary text-white border-transparent' : 'border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-primary hover:text-primary'"
                        default-classes="w-full text-left px-4 py-2 text-sm border rounded-lg transition-all">
                        {{ \App\Support\States::fullName($locState) }}
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>
        </x-dl.wrapper>
        @if ($this->locationsFiltered->isNotEmpty())
            <x-dl.wrapper slug="__SLUG__" prefix="locations_list"
                default-classes="md:col-span-2 space-y-4"
                >
                @foreach ($this->locationsFiltered as $location)
                    <x-dl.card slug="__SLUG__" prefix="location_card"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center gap-4 p-4 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary transition-colors">
                        @if ($location->photoUrl())
                            <x-dl.wrapper slug="__SLUG__" prefix="location_thumbnail"
                                default-classes="size-16 rounded-lg shrink-0 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                <img src="{{ $location->photoUrl() }}" alt="{{ $location->name }}" class="w-full h-full object-cover">
                            </x-dl.wrapper>
                        @endif
                        <x-dl.group slug="__SLUG__" prefix="location_info"
                            default-classes="flex-1 min-w-0">
                            <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                                default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-0.5">
                                {{ $location->name }}
                            </x-dl.wrapper>
                            @if ($location->address)
                                <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="p"
                                    default-classes="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                                </x-dl.wrapper>
                            @endif
                        </x-dl.group>
                        @if ($location->phone)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_phone" tag="a"
                                href="tel:{{ $location->phone }}"
                                default-classes="text-xs text-primary hover:text-primary/80 transition-colors shrink-0">
                                {{ $location->phone }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.card>
                @endforeach
            </x-dl.wrapper>
        @endif
    </x-dl.wrapper>
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

{{--
@name Locations - List
@description Vertical list of locations with address and contact details.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Visit us at any of our locations."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_list"
            default-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
            note="Content is pulled from the <a href='/dashboard/locations' class='text-primary underline hover:text-primary/80'>Locations</a> page.">
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_item"
                    default-classes="py-6 flex items-start justify-between gap-6">
                    <x-dl.group slug="__SLUG__" prefix="location_info"
                        default-classes="">
                        <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                            default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-1">
                            {{ $location->name }}
                        </x-dl.wrapper>
                        @if ($location->address)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-2">
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
                    </x-dl.group>
                    @if ($location->hours)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_hours" tag="p"
                            default-classes="text-sm text-zinc-500 dark:text-zinc-400 shrink-0 text-right">
                            {{ $location->hours }}
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

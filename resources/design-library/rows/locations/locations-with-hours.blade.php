{{--
@name Locations - With Hours
@description Location cards prominently featuring business hours.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Store Hours & Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Visit us during our operating hours."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_grid"
            default-classes="grid sm:grid-cols-2 md:grid-cols-3 gap-6"
            >
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_card"
                    default-classes="bg-white dark:bg-zinc-900 rounded-card border border-zinc-200 dark:border-zinc-700 p-6">
                    <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                        default-classes="text-base font-bold text-zinc-900 dark:text-white mb-3">
                        {{ $location->name }}
                    </x-dl.wrapper>
                    @if ($location->address)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_address_row"
                            default-classes="flex items-start gap-2 mb-2">
                            <x-dl.icon slug="__SLUG__" prefix="addr_icon" name="map-pin"
                                default-classes="size-4 text-zinc-400 mt-0.5 shrink-0" />
                            <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="span"
                                default-classes="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                            </x-dl.wrapper>
                        </x-dl.wrapper>
                    @endif
                    @if ($location->hours)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_hours_row"
                            default-classes="flex items-start gap-2 mb-2">
                            <x-dl.icon slug="__SLUG__" prefix="hours_icon" name="clock"
                                default-classes="size-4 text-primary mt-0.5 shrink-0" />
                            <x-dl.wrapper slug="__SLUG__" prefix="location_hours" tag="span"
                                default-classes="text-sm font-medium text-primary">
                                {{ $location->hours }}
                            </x-dl.wrapper>
                        </x-dl.wrapper>
                    @endif
                    @if ($location->phone)
                        <x-dl.wrapper slug="__SLUG__" prefix="location_phone_row"
                            default-classes="flex items-center gap-2">
                            <x-dl.icon slug="__SLUG__" prefix="phone_icon" name="phone"
                                default-classes="size-4 text-zinc-400 shrink-0" />
                            <x-dl.wrapper slug="__SLUG__" prefix="location_phone" tag="a"
                                href="tel:{{ $location->phone }}"
                                default-classes="text-sm text-zinc-600 dark:text-zinc-300 hover:text-primary transition-colors">
                                {{ $location->phone }}
                            </x-dl.wrapper>
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

{{--
@name Locations - Contact
@description Location list with contact details and a "Get Directions" link.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Contact Our Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Reach out to any of our locations directly."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_grid"
            default-classes="grid md:grid-cols-2 gap-6"
            >
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_card"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-6 hover:border-primary transition-colors">
                    <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                        default-classes="text-base font-bold text-zinc-900 dark:text-white mb-4">
                        {{ $location->name }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="location_details"
                        default-classes="space-y-2 mb-5">
                        @if ($location->address)
                            <x-dl.wrapper slug="__SLUG__" prefix="loc_addr"
                                default-classes="flex items-start gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <x-dl.icon slug="__SLUG__" prefix="addr_icon" name="map-pin"
                                    default-classes="size-4 text-primary mt-0.5 shrink-0" />
                                {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                            </x-dl.wrapper>
                        @endif
                        @if ($location->phone)
                            <x-dl.wrapper slug="__SLUG__" prefix="loc_phone"
                                default-classes="flex items-center gap-2">
                                <x-dl.icon slug="__SLUG__" prefix="phone_icon" name="phone"
                                    default-classes="size-4 text-primary shrink-0" />
                                <x-dl.wrapper slug="__SLUG__" prefix="phone_link" tag="a"
                                    href="tel:{{ $location->phone }}"
                                    default-classes="text-sm text-zinc-600 dark:text-zinc-300 hover:text-primary transition-colors">
                                    {{ $location->phone }}
                                </x-dl.wrapper>
                            </x-dl.wrapper>
                        @endif
                        @if ($location->hours)
                            <x-dl.wrapper slug="__SLUG__" prefix="loc_hours"
                                default-classes="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <x-dl.icon slug="__SLUG__" prefix="hours_icon" name="clock"
                                    default-classes="size-4 text-primary shrink-0" />
                                {{ $location->hours }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.wrapper>
                    @if ($location->address)
                        <x-dl.wrapper slug="__SLUG__" prefix="directions_link" tag="a"
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location->address . ', ' . $location->city_state_zip) }}"
                            target="_blank"
                            default-classes="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                            Get Directions →
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

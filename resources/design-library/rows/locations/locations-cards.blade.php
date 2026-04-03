{{--
@name Locations - Cards
@description Location cards with photo, name, and details in a grid.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Find a Location"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="We're in your neighborhood."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_grid"
            default-classes="grid sm:grid-cols-2 md:grid-cols-3 gap-6"
            >
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_card"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:border-primary transition-colors">
                    @if ($location->photoUrl())
                        <x-dl.wrapper slug="__SLUG__" prefix="location_image_wrapper"
                            default-classes="aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $location->photoUrl() }}" alt="{{ $location->name }}" class="w-full h-full object-cover">
                        </x-dl.wrapper>
                    @endif
                    <x-dl.wrapper slug="__SLUG__" prefix="location_body"
                        default-classes="p-5">
                        <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                            default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-1">
                            {{ $location->name }}
                        </x-dl.wrapper>
                        @if ($location->address)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                            </x-dl.wrapper>
                        @endif
                        @if ($location->phone)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_phone" tag="a"
                                href="tel:{{ $location->phone }}"
                                default-classes="mt-2 block text-sm text-primary hover:text-primary/80 transition-colors">
                                {{ $location->phone }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.wrapper>
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

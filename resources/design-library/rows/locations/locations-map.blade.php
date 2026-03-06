{{--
@name Locations - Map
@description Location list with an embedded map placeholder.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Locations"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find the location nearest to you."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    <x-dl.wrapper slug="__SLUG__" prefix="content_grid"
        default-classes="grid md:grid-cols-2 gap-8">
        @if ($this->locationsFiltered->isNotEmpty())
            <x-dl.wrapper slug="__SLUG__" prefix="list_panel"
                default-classes="space-y-4 overflow-y-auto max-h-[600px] pr-2"
                note="Content is pulled from the <a href='/dashboard/locations' class='text-primary underline hover:text-primary/80'>Locations</a> page.">
                @foreach ($this->locationsFiltered as $location)
                    <x-dl.card slug="__SLUG__" prefix="location_card"
                        default-classes="p-4 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary transition-colors cursor-pointer">
                        <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                            default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                            {{ $location->name }}
                        </x-dl.wrapper>
                        @if ($location->address)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_address" tag="p"
                                default-classes="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $location->address }}@if ($location->city_state_zip), {{ $location->city_state_zip }}@endif
                            </x-dl.wrapper>
                        @endif
                        @if ($location->phone)
                            <x-dl.wrapper slug="__SLUG__" prefix="location_phone" tag="a"
                                href="tel:{{ $location->phone }}"
                                default-classes="text-xs text-primary hover:text-primary/80 transition-colors mt-1 block"
                                @click.stop>
                                {{ $location->phone }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.card>
                @endforeach
            </x-dl.wrapper>
        @endif
        <x-dl.wrapper slug="__SLUG__" prefix="map_wrapper"
            default-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 min-h-[400px]">
            <x-dl.wrapper slug="__SLUG__" prefix="map_embed" tag="iframe"
                src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d5000000!2d-98.5795!3d39.8283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1"
                width="100%" height="100%" style="border:0;min-height:400px;" allowfullscreen="" loading="lazy"
                default-classes="w-full h-full" />
        </x-dl.wrapper>
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

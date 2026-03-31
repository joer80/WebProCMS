{{--
@name Locations - Minimal
@description Simple text-only location list with no images.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Locations"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-8" />
    @if ($this->locationsFiltered->isNotEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="locations_list"
            default-classes="grid md:grid-cols-2 gap-x-12 gap-y-6"
            >
            @foreach ($this->locationsFiltered as $location)
                <x-dl.card slug="__SLUG__" prefix="location_item"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="">
                    <x-dl.wrapper slug="__SLUG__" prefix="location_name" tag="h3"
                        default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-0.5">
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
                            default-classes="text-xs text-primary hover:text-primary/80 transition-colors mt-0.5 block">
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

{{--
@name Locations - Featured
@description One large featured location with details prominently displayed.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Flagship Location"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Come see us in person."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    @php $featured = $this->locationsFiltered->first(); @endphp
    @if ($featured)
        <x-dl.wrapper slug="__SLUG__" prefix="featured_card"
            default-classes="grid md:grid-cols-2 gap-8 rounded-card overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900"
            >
            @if ($featured->photoUrl())
                <x-dl.wrapper slug="__SLUG__" prefix="featured_image"
                    default-classes="aspect-auto md:aspect-auto overflow-hidden bg-zinc-100 dark:bg-zinc-800 min-h-64">
                    <img src="{{ $featured->photoUrl() }}" alt="{{ $featured->name }}" class="w-full h-full object-cover">
                </x-dl.wrapper>
            @endif
            <x-dl.wrapper slug="__SLUG__" prefix="featured_details"
                default-classes="p-8 flex flex-col justify-center">
                <x-dl.wrapper slug="__SLUG__" prefix="featured_name" tag="h3"
                    default-classes="text-2xl font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $featured->name }}
                </x-dl.wrapper>
                @if ($featured->address)
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_address"
                        default-classes="flex items-start gap-3 mb-3">
                        <x-dl.icon slug="__SLUG__" prefix="address_icon" name="map-pin"
                            default-classes="size-4 text-primary mt-0.5 shrink-0" />
                        <x-dl.wrapper slug="__SLUG__" prefix="address_text" tag="span"
                            default-classes="text-zinc-600 dark:text-zinc-300 text-sm">
                            {{ $featured->address }}@if ($featured->city_state_zip), {{ $featured->city_state_zip }}@endif
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                @endif
                @if ($featured->phone)
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_phone"
                        default-classes="flex items-center gap-3 mb-3">
                        <x-dl.icon slug="__SLUG__" prefix="phone_icon" name="phone"
                            default-classes="size-4 text-primary shrink-0" />
                        <x-dl.wrapper slug="__SLUG__" prefix="phone_link" tag="a"
                            href="tel:{{ $featured->phone }}"
                            default-classes="text-zinc-600 dark:text-zinc-300 text-sm hover:text-primary transition-colors">
                            {{ $featured->phone }}
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                @endif
                @if ($featured->hours)
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_hours"
                        default-classes="flex items-center gap-3">
                        <x-dl.icon slug="__SLUG__" prefix="hours_icon" name="clock"
                            default-classes="size-4 text-primary shrink-0" />
                        <x-dl.wrapper slug="__SLUG__" prefix="hours_text" tag="span"
                            default-classes="text-zinc-600 dark:text-zinc-300 text-sm">
                            {{ $featured->hours }}
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                @endif
            </x-dl.wrapper>
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

{{--
@name Events - Card Grid
@description Grid of upcoming event cards with featured image, date badge, title, and excerpt.
@sort 25
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Upcoming Events"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/events"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </div>
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Don't miss what's coming up."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal -mt-6 mb-10" />

    @if ($this->eventsGridData->isEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="grid_empty" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No upcoming events at this time.
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="events_grid"
            default-classes="grid md:grid-cols-3 gap-6">
            @foreach ($this->eventsGridData as $event)
                <x-dl.card slug="__SLUG__" prefix="grid_card" tag="a"
                    wire:key="egrid-{{ $event->id }}"
                    href="{{ route('events.show', $event->slug) }}"
                    default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:shadow-card transition-shadow group">
                    {{-- Image area --}}
                    <x-dl.wrapper slug="__SLUG__" prefix="card_image_wrapper"
                        default-classes="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        @if ($event->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($event->featured_image) }}"
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">No image</div>
                        @endif
                        {{-- Date badge overlay --}}
                        <x-dl.group slug="__SLUG__" prefix="card_date_badge"
                            default-classes="absolute bottom-0 left-0 bg-primary text-white px-2 py-1 text-xs font-semibold">
                            {{ $event->start_date->format('D M j') }}
                        </x-dl.group>
                    </x-dl.wrapper>
                    {{-- Card body --}}
                    <x-dl.group slug="__SLUG__" prefix="card_body"
                        default-classes="p-4">
                        <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                            default-classes="font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-primary transition-colors mb-1">
                            {{ $event->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="card_meta" tag="p"
                            default-classes="text-xs text-zinc-500 dark:text-zinc-400 mb-2">
                            @if ($event->is_all_day)
                                All Day
                            @else
                                {{ $event->start_date->format('g:i A') }}
                            @endif
                            @if ($event->venue_name)
                                · {{ $event->venue_name }}
                            @endif
                        </x-dl.wrapper>
                        @if ($event->excerpt)
                            <x-dl.wrapper slug="__SLUG__" prefix="card_excerpt" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                {{ $event->excerpt }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsGridDataProperty(): \Illuminate\Database\Eloquent\Collection
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->upcoming()
        ->orderBy('start_date')
        ->limit(6)
        ->get();
}
@endphp
--}}

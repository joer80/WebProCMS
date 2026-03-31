{{--
@name Events - Mini List
@description Compact events list showing date, time, title, and venue. Great for sidebars or secondary sections.
@sort 35
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Upcoming Events"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="What's coming up next."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-8" />

    @if ($this->eventsMiniListData->isEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="mini_empty" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No upcoming events at this time.
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="mini_list"
            default-classes="divide-y divide-zinc-200 dark:divide-zinc-700">
            @foreach ($this->eventsMiniListData as $event)
                <x-dl.card slug="__SLUG__" prefix="mini_item" tag="a"
                    wire:key="emini-{{ $event->id }}"
                    href="{{ route('events.show', $event->slug) }}"
                    default-classes="flex items-center gap-3 py-3 group">
                    <x-dl.group slug="__SLUG__" prefix="mini_date_badge"
                        default-classes="flex flex-col items-center justify-center w-10 h-10 rounded bg-primary/10 text-primary shrink-0 text-center">
                        <span class="text-xs font-semibold uppercase leading-none">{{ $event->start_date->format('M') }}</span>
                        <span class="text-sm font-bold leading-none">{{ $event->start_date->format('j') }}</span>
                    </x-dl.group>
                    <x-dl.group slug="__SLUG__" prefix="mini_item_content"
                        default-classes="flex-1 min-w-0">
                        <x-dl.wrapper slug="__SLUG__" prefix="mini_item_title" tag="span"
                            default-classes="block font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-primary transition-colors truncate text-sm">
                            {{ $event->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="mini_item_meta" tag="span"
                            default-classes="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            @if ($event->is_all_day)
                                All Day
                            @else
                                {{ $event->start_date->format('g:i A') }}
                            @endif
                            @if ($event->venue_name)
                                · {{ $event->venue_name }}
                            @endif
                        </x-dl.wrapper>
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    @endif

    <x-dl.link slug="__SLUG__" prefix="view_all"
        default-label="View all events →"
        default-url="/events"
        default-classes="inline-block mt-6 text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
</x-dl.section>
{{--
@php
/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsMiniListDataProperty(): \Illuminate\Database\Eloquent\Collection
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->upcoming()
        ->orderBy('start_date')
        ->limit(8)
        ->get();
}
@endphp
--}}

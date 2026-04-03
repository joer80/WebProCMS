{{--
@name Events - Day View
@description Date picker showing events for a selected day.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Events"
        default-tag="h1"
        default-classes="text-4xl font-semibold leading-tight mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Select a date to see what's on."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    {{-- Date picker --}}
    <x-dl.wrapper slug="__SLUG__" prefix="day_picker_wrapper"
        default-classes="mb-8">
        <x-dl.wrapper slug="__SLUG__" prefix="day_picker_label" tag="label"
            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
            Select a date
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="day_picker_input" tag="input"
            wire:model.live="eventsSelectedDay"
            type="date"
            default-classes="border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-primary/50" />
    </x-dl.wrapper>

    @if ($eventsSelectedDay)
        @if ($this->eventsDayView->isEmpty())
            <x-dl.wrapper slug="__SLUG__" prefix="day_empty" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                No events on this day.
            </x-dl.wrapper>
        @else
            <x-dl.wrapper slug="__SLUG__" prefix="day_items"
                default-classes="space-y-3">
                @foreach ($this->eventsDayView as $event)
                    <x-dl.card slug="__SLUG__" prefix="day_item" tag="a"
                        wire:key="dayview-{{ $event->id }}"
                        href="{{ route('events.show', $event->slug) }}"
                        default-classes="flex items-center gap-4 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors group">
                        <x-dl.wrapper slug="__SLUG__" prefix="day_item_time" tag="span"
                            default-classes="text-sm font-medium text-zinc-500 dark:text-zinc-400 w-20 shrink-0">
                            @if ($event->is_all_day)
                                All Day
                            @else
                                {{ $event->start_date->format('g:i A') }}
                            @endif
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="day_item_title" tag="span"
                            default-classes="font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors flex-1 min-w-0 truncate">
                            {{ $event->title }}
                        </x-dl.wrapper>
                        @if ($event->venue_name)
                            <x-dl.wrapper slug="__SLUG__" prefix="day_item_venue" tag="span"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 hidden sm:block shrink-0">
                                {{ $event->venue_name }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.card>
                @endforeach
            </x-dl.wrapper>
        @endif
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="day_prompt" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            Pick a date to see events.
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
#[\Livewire\Attributes\Url(as: 'day')]
public string $eventsSelectedDay = '';

/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsDayViewProperty(): \Illuminate\Database\Eloquent\Collection
{
    if (! $this->eventsSelectedDay) {
        return \App\Models\Event::query()->whereRaw('1=0')->get();
    }

    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->whereDate('start_date', $this->eventsSelectedDay)
        ->orderBy('start_date')
        ->get();
}
@endphp
--}}

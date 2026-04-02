<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Browse upcoming and past events.'])] #[Title('Events')] class extends Component {
    public string $pageName = 'Events';
    // ROW:php:start:events-list:ENimZp
    use \Livewire\WithPagination;
    
    #[\Livewire\Attributes\Url(as: 'view')]
    public string $eventViewMode = 'list';
    
    #[\Livewire\Attributes\Url(as: 'tab')]
    public string $eventTab = 'upcoming';
    
    #[\Livewire\Attributes\Url(as: 'q')]
    public string $eventSearch = '';
    
    #[\Livewire\Attributes\Url(as: 'month')]
    public string $eventCurrentMonth = '';
    
    #[\Livewire\Attributes\Url(as: 'day')]
    public string $eventSelectedDay = '';
    
    public function setEventViewMode(string $mode): void
    {
        $this->eventViewMode = $mode;
        $this->resetPage();
    }
    
    public function setEventTab(string $tab): void
    {
        $this->eventTab = $tab;
        $this->resetPage();
    }
    
    public function previousEventMonth(): void
    {
        $month = $this->eventCurrentMonth ?: now()->format('Y-m');
        $this->eventCurrentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');
    }
    
    public function nextEventMonth(): void
    {
        $month = $this->eventCurrentMonth ?: now()->format('Y-m');
        $this->eventCurrentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->addMonth()->format('Y-m');
    }
    
    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Event> */
    public function getEventsListProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Event::query()
            ->published()
            ->parent()
            ->when($this->eventTab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($this->eventTab === 'past', fn ($q) => $q->past())
            ->when($this->eventSearch, fn ($q) => $q->where(function ($q): void {
                $q->where('title', 'like', "%{$this->eventSearch}%")
                    ->orWhere('venue_name', 'like', "%{$this->eventSearch}%");
            }))
            ->orderBy('start_date', $this->eventTab === 'past' ? 'desc' : 'asc')
            ->paginate(10);
    }
    
    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
    public function getEventsMonthProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $month = $this->eventCurrentMonth ?: now()->format('Y-m');
        $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();
    
        return \App\Models\Event::query()
            ->published()
            ->parent()
            ->where('start_date', '>=', $start)
            ->where('start_date', '<=', $end)
            ->orderBy('start_date')
            ->get();
    }
    
    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
    public function getEventsDayProperty(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->eventSelectedDay) {
            return \App\Models\Event::query()->whereRaw('1=0')->get();
        }
    
        $day = \Carbon\Carbon::parse($this->eventSelectedDay);
    
        return \App\Models\Event::query()
            ->published()
            ->parent()
            ->whereDate('start_date', $day)
            ->orderBy('start_date')
            ->get();
    }
    // ROW:php:end:events-list:ENimZp
}; ?>
<div>{{-- ROW:start:page-title-banner:EwZBCC:shared=1 --}}
@include('shared-rows.page-title-banner-EwZBCC')
{{-- ROW:end:page-title-banner:EwZBCC --}}

{{-- ROW:start:events-list:ENimZp --}}
<x-dl.section slug="events-list:ENimZp"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="events-list:ENimZp" prefix="headline" default="Events"
        default-tag="h1"
        default-classes="text-4xl font-semibold leading-tight mb-4" />
    <x-dl.subheadline slug="events-list:ENimZp" prefix="subheadline" default="Browse our upcoming and past events."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    {{-- View mode switcher --}}
    <x-dl.wrapper slug="events-list:ENimZp" prefix="view_switcher"
        default-classes="flex items-center gap-2 mb-8">
        <x-dl.wrapper slug="events-list:ENimZp" prefix="view_list_btn" tag="button"
            wire:click="setEventViewMode('list')"
            :class="$eventViewMode === 'list' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
            default-classes="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-sm border transition-all">
            List
        </x-dl.wrapper>
        <x-dl.wrapper slug="events-list:ENimZp" prefix="view_month_btn" tag="button"
            wire:click="setEventViewMode('month')"
            :class="$eventViewMode === 'month' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
            default-classes="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-sm border transition-all">
            Month
        </x-dl.wrapper>
        <x-dl.wrapper slug="events-list:ENimZp" prefix="view_day_btn" tag="button"
            wire:click="setEventViewMode('day')"
            :class="$eventViewMode === 'day' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
            default-classes="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-sm border transition-all">
            Day
        </x-dl.wrapper>
    </x-dl.wrapper>

    {{-- LIST VIEW --}}
    @if ($eventViewMode === 'list')
        {{-- Search + tabs --}}
        <x-dl.wrapper slug="events-list:ENimZp" prefix="list_filters"
            default-classes="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
            <x-dl.group slug="events-list:ENimZp" prefix="list_search_wrapper"
                default-classes="relative flex-1 max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 inset-s-0 flex items-center ps-3 text-zinc-400/75">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
                </div>
                <x-dl.wrapper slug="events-list:ENimZp" prefix="list_search_input" tag="input"
                    wire:model.live.debounce.300ms="eventSearch"
                    type="search"
                    placeholder="Search events…"
                    default-classes="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg bg-white text-base sm:text-sm py-2 h-10 ps-10 pe-3 text-zinc-700 placeholder-zinc-400 dark:bg-white/10 dark:border-white/10 dark:text-zinc-300 dark:placeholder-zinc-400" />
            </x-dl.group>
            <x-dl.group slug="events-list:ENimZp" prefix="list_tab_buttons"
                default-classes="flex items-center gap-2">
                <x-dl.wrapper slug="events-list:ENimZp" prefix="list_tab_upcoming" tag="button"
                    wire:click="setEventTab('upcoming')"
                    :class="$eventTab === 'upcoming' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                    default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                    Upcoming
                </x-dl.wrapper>
                <x-dl.wrapper slug="events-list:ENimZp" prefix="list_tab_past" tag="button"
                    wire:click="setEventTab('past')"
                    :class="$eventTab === 'past' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                    default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                    Past
                </x-dl.wrapper>
            </x-dl.group>
        </x-dl.wrapper>

        @if ($this->eventsList->isEmpty())
            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_empty" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                No events found.
            </x-dl.wrapper>
        @else
            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_items"
                default-classes="space-y-4">
                @foreach ($this->eventsList as $event)
                    <x-dl.card slug="events-list:ENimZp" prefix="list_item" tag="a"
                        wire:key="event-{{ $event->id }}"
                        href="{{ route('events.show', $event->slug) }}"
                        default-classes="flex items-start gap-4 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors group">
                        <x-dl.group slug="events-list:ENimZp" prefix="list_date_badge"
                            default-classes="flex flex-col items-center justify-center w-14 h-14 rounded-lg bg-primary text-white shrink-0 text-center">
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_date_month" tag="span"
                                default-classes="text-xs font-semibold uppercase leading-none">
                                {{ $event->start_date->format('M') }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_date_day" tag="span"
                                default-classes="text-2xl font-bold leading-none">
                                {{ $event->start_date->format('j') }}
                            </x-dl.wrapper>
                        </x-dl.group>
                        <x-dl.group slug="events-list:ENimZp" prefix="list_item_content"
                            default-classes="flex-1 min-w-0">
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_item_title" tag="h3"
                                default-classes="font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors truncate">
                                {{ $event->title }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_item_meta" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
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
                                <x-dl.wrapper slug="events-list:ENimZp" prefix="list_item_excerpt" tag="p"
                                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-1">
                                    {{ $event->excerpt }}
                                </x-dl.wrapper>
                            @endif
                        </x-dl.group>
                        <x-dl.wrapper slug="events-list:ENimZp" prefix="list_item_arrow" tag="span"
                            default-classes="shrink-0 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.wrapper>

            <x-dl.wrapper slug="events-list:ENimZp" prefix="list_pagination"
                default-classes="mt-8">
                {{ $this->eventsList->links() }}
            </x-dl.wrapper>
        @endif
    @endif

    {{-- MONTH VIEW --}}
    @if ($eventViewMode === 'month')
        @php
            $currentMonthStr = $eventCurrentMonth ?: now()->format('Y-m');
            $monthCarbon = \Carbon\Carbon::createFromFormat('Y-m', $currentMonthStr)->startOfMonth();
            $monthEvents = $this->eventsMonth;
            $eventsJson = $monthEvents->map(fn ($e) => [
                'title' => $e->title,
                'slug' => $e->slug,
                'start' => $e->start_date->format('Y-m-d'),
                'time' => $e->is_all_day ? 'All Day' : $e->start_date->format('g:i A'),
            ])->values()->toArray();
        @endphp
        <x-dl.wrapper slug="events-list:ENimZp" prefix="month_calendar"
            default-classes="w-full"
            data-events="{{ e(json_encode($eventsJson)) }}"
            data-current-month="{{ $currentMonthStr }}"
            x-data="{
                events: [],
                currentMonth: '',
                init() {
                    this.events = JSON.parse(this.$el.dataset.events || '[]');
                    this.currentMonth = this.$el.dataset.currentMonth;
                }
            }">
            {{-- Calendar header --}}
            <x-dl.wrapper slug="events-list:ENimZp" prefix="month_header"
                default-classes="flex items-center justify-between mb-4">
                <x-dl.wrapper slug="events-list:ENimZp" prefix="month_title" tag="h2"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $monthCarbon->format('F Y') }}
                </x-dl.wrapper>
                <x-dl.group slug="events-list:ENimZp" prefix="month_nav_buttons"
                    default-classes="flex items-center gap-2">
                    <x-dl.wrapper slug="events-list:ENimZp" prefix="month_prev_btn" tag="button"
                        wire:click="previousEventMonth"
                        default-classes="p-1.5 rounded border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-zinc-600 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="events-list:ENimZp" prefix="month_next_btn" tag="button"
                        wire:click="nextEventMonth"
                        default-classes="p-1.5 rounded border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-zinc-600 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.wrapper>

            {{-- Day headers --}}
            <x-dl.wrapper slug="events-list:ENimZp" prefix="month_day_headers"
                default-classes="grid grid-cols-7 mb-1">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <x-dl.wrapper slug="events-list:ENimZp" prefix="month_day_header" tag="div"
                        default-classes="text-xs font-medium text-zinc-500 dark:text-zinc-400 text-center py-2">
                        {{ $dayName }}
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>

            {{-- Calendar grid --}}
            @php
                $today = now()->format('Y-m-d');
                $startOfCal = $monthCarbon->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $cells = [];
                for ($i = 0; $i < 42; $i++) {
                    $cells[] = $startOfCal->copy()->addDays($i);
                }
                $eventsByDate = collect($eventsJson)->groupBy('start');
            @endphp
            <x-dl.wrapper slug="events-list:ENimZp" prefix="month_grid"
                default-classes="grid grid-cols-7 border-l border-t border-zinc-200 dark:border-zinc-700">
                @foreach ($cells as $cell)
                    @php
                        $cellDate = $cell->format('Y-m-d');
                        $isCurrentMonth = $cell->month === $monthCarbon->month;
                        $isToday = $cellDate === $today;
                        $dayEvents = $eventsByDate[$cellDate] ?? collect();
                    @endphp
                    <x-dl.wrapper slug="events-list:ENimZp" prefix="month_cell" tag="div"
                        default-classes="min-h-24 p-1 border-r border-b border-zinc-200 dark:border-zinc-700 {{ $isCurrentMonth ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-50 dark:bg-zinc-800/30' }}">
                        <x-dl.wrapper slug="events-list:ENimZp" prefix="month_cell_num" tag="span"
                            default-classes="inline-flex items-center justify-center size-6 text-xs font-medium mb-1 {{ $isToday ? 'rounded-full bg-primary text-white' : ($isCurrentMonth ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-600') }}">
                            {{ $cell->day }}
                        </x-dl.wrapper>
                        @foreach ($dayEvents as $ev)
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="month_event_label" tag="a"
                                href="{{ route('events.show', $ev['slug']) }}"
                                default-classes="block truncate text-xs px-1 py-0.5 rounded bg-primary/10 text-primary hover:bg-primary/20 transition-colors mb-0.5">
                                {{ $ev['title'] }}
                            </x-dl.wrapper>
                        @endforeach
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>
        </x-dl.wrapper>
    @endif

    {{-- DAY VIEW --}}
    @if ($eventViewMode === 'day')
        <x-dl.wrapper slug="events-list:ENimZp" prefix="day_picker_wrapper"
            default-classes="mb-6">
            <x-dl.wrapper slug="events-list:ENimZp" prefix="day_picker_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Select a date
            </x-dl.wrapper>
            <x-dl.wrapper slug="events-list:ENimZp" prefix="day_picker_input" tag="input"
                wire:model.live="eventSelectedDay"
                type="date"
                default-classes="border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-primary/50" />
        </x-dl.wrapper>

        @if ($eventSelectedDay)
            @php $dayEvents = $this->eventsDay; @endphp
            @if ($dayEvents->isEmpty())
                <x-dl.wrapper slug="events-list:ENimZp" prefix="day_empty" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                    No events on this day.
                </x-dl.wrapper>
            @else
                <x-dl.wrapper slug="events-list:ENimZp" prefix="day_items"
                    default-classes="space-y-3">
                    @foreach ($dayEvents as $event)
                        <x-dl.card slug="events-list:ENimZp" prefix="day_item" tag="a"
                            wire:key="day-event-{{ $event->id }}"
                            href="{{ route('events.show', $event->slug) }}"
                            default-classes="flex items-center gap-4 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors group">
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="day_item_time" tag="span"
                                default-classes="text-sm font-medium text-zinc-500 dark:text-zinc-400 w-20 shrink-0">
                                @if ($event->is_all_day)
                                    All Day
                                @else
                                    {{ $event->start_date->format('g:i A') }}
                                @endif
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="events-list:ENimZp" prefix="day_item_title" tag="span"
                                default-classes="font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                                {{ $event->title }}
                            </x-dl.wrapper>
                            @if ($event->venue_name)
                                <x-dl.wrapper slug="events-list:ENimZp" prefix="day_item_venue" tag="span"
                                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 hidden sm:block">
                                    {{ $event->venue_name }}
                                </x-dl.wrapper>
                            @endif
                        </x-dl.card>
                    @endforeach
                </x-dl.wrapper>
            @endif
        @else
            <x-dl.wrapper slug="events-list:ENimZp" prefix="day_prompt" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                Pick a date to see events.
            </x-dl.wrapper>
        @endif
    @endif
</x-dl.section>
{{--
@php
use \Livewire\WithPagination;

#[\Livewire\Attributes\Url(as: 'view')]
public string $eventViewMode = 'list';

#[\Livewire\Attributes\Url(as: 'tab')]
public string $eventTab = 'upcoming';

#[\Livewire\Attributes\Url(as: 'q')]
public string $eventSearch = '';

#[\Livewire\Attributes\Url(as: 'month')]
public string $eventCurrentMonth = '';

#[\Livewire\Attributes\Url(as: 'day')]
public string $eventSelectedDay = '';

public function setEventViewMode(string $mode): void
{
    $this->eventViewMode = $mode;
    $this->resetPage();
}

public function setEventTab(string $tab): void
{
    $this->eventTab = $tab;
    $this->resetPage();
}

public function previousEventMonth(): void
{
    $month = $this->eventCurrentMonth ?: now()->format('Y-m');
    $this->eventCurrentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');
}

public function nextEventMonth(): void
{
    $month = $this->eventCurrentMonth ?: now()->format('Y-m');
    $this->eventCurrentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->addMonth()->format('Y-m');
}

/** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Event> */
public function getEventsListProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->when($this->eventTab === 'upcoming', fn ($q) => $q->upcoming())
        ->when($this->eventTab === 'past', fn ($q) => $q->past())
        ->when($this->eventSearch, fn ($q) => $q->where(function ($q): void {
            $q->where('title', 'like', "%{$this->eventSearch}%")
                ->orWhere('venue_name', 'like', "%{$this->eventSearch}%");
        }))
        ->orderBy('start_date', $this->eventTab === 'past' ? 'desc' : 'asc')
        ->paginate(10);
}

/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsMonthProperty(): \Illuminate\Database\Eloquent\Collection
{
    $month = $this->eventCurrentMonth ?: now()->format('Y-m');
    $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end = $start->copy()->endOfMonth();

    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->where('start_date', '>=', $start)
        ->where('start_date', '<=', $end)
        ->orderBy('start_date')
        ->get();
}

/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsDayProperty(): \Illuminate\Database\Eloquent\Collection
{
    if (! $this->eventSelectedDay) {
        return \App\Models\Event::query()->whereRaw('1=0')->get();
    }

    $day = \Carbon\Carbon::parse($this->eventSelectedDay);

    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->whereDate('start_date', $day)
        ->orderBy('start_date')
        ->get();
}
@endphp
--}}
{{-- ROW:end:events-list:ENimZp --}}
</div>

{{--
@name Events - List View
@description Upcoming and past events list with search and tab filters. No calendar views.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Events"
        default-tag="h1"
        default-classes="text-4xl font-semibold leading-tight mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Browse our upcoming and past events."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    {{-- Controls: search left, tabs right --}}
    <x-dl.wrapper slug="__SLUG__" prefix="list_controls"
        default-classes="grid sm:grid-cols-2 gap-4 mb-8">
        <x-dl.group slug="__SLUG__" prefix="list_search_wrapper"
            default-classes="relative">
            <div class="pointer-events-none absolute inset-y-0 inset-s-0 flex items-center ps-3 text-zinc-400/75">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
            </div>
            <x-dl.wrapper slug="__SLUG__" prefix="list_search_input" tag="input"
                wire:model.live.debounce.300ms="eventsListSearch"
                type="search"
                placeholder="Search events…"
                default-classes="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg bg-white text-base sm:text-sm py-2 h-10 ps-10 pe-3 text-zinc-700 placeholder-zinc-400 dark:bg-white/10 dark:border-white/10 dark:text-zinc-300 dark:placeholder-zinc-400" />
        </x-dl.group>
        <x-dl.group slug="__SLUG__" prefix="list_tab_buttons"
            default-classes="flex items-center justify-end gap-2">
            <x-dl.wrapper slug="__SLUG__" prefix="list_tab_upcoming" tag="button"
                wire:click="setEventsListTab('upcoming')"
                :class="$eventsListTab === 'upcoming' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                Upcoming
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="list_tab_past" tag="button"
                wire:click="setEventsListTab('past')"
                :class="$eventsListTab === 'past' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                default-classes="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all">
                Past
            </x-dl.wrapper>
        </x-dl.group>
    </x-dl.wrapper>

    @if ($this->eventsUpcomingList->isEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="list_empty" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No events found.
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="list_items"
            default-classes="space-y-4">
            @foreach ($this->eventsUpcomingList as $event)
                <x-dl.card slug="__SLUG__" prefix="list_item" tag="a"
                    wire:key="evlist-{{ $event->id }}"
                    href="{{ route('events.show', $event->slug) }}"
                    default-classes="flex items-start gap-4 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors group">
                    <x-dl.group slug="__SLUG__" prefix="list_date_badge"
                        default-classes="flex flex-col items-center justify-center w-14 h-14 rounded-lg bg-primary text-white shrink-0 text-center">
                        <x-dl.wrapper slug="__SLUG__" prefix="list_date_month" tag="span"
                            default-classes="text-xs font-semibold uppercase leading-none">
                            {{ $event->start_date->format('M') }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="list_date_day" tag="span"
                            default-classes="text-2xl font-bold leading-none">
                            {{ $event->start_date->format('j') }}
                        </x-dl.wrapper>
                    </x-dl.group>
                    <x-dl.group slug="__SLUG__" prefix="list_item_content"
                        default-classes="flex-1 min-w-0">
                        <x-dl.wrapper slug="__SLUG__" prefix="list_item_title" tag="h3"
                            default-classes="font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors truncate">
                            {{ $event->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="list_item_meta" tag="p"
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
                            <x-dl.wrapper slug="__SLUG__" prefix="list_item_excerpt" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-1">
                                {{ $event->excerpt }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.group>
                    <x-dl.wrapper slug="__SLUG__" prefix="list_item_arrow" tag="span"
                        default-classes="shrink-0 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>

        <x-dl.wrapper slug="__SLUG__" prefix="list_pagination"
            default-classes="mt-8">
            {{ $this->eventsUpcomingList->links() }}
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
use \Livewire\WithPagination;

#[\Livewire\Attributes\Url(as: 'q')]
public string $eventsListSearch = '';

#[\Livewire\Attributes\Url(as: 'tab')]
public string $eventsListTab = 'upcoming';

public function setEventsListTab(string $tab): void
{
    $this->eventsListTab = $tab;
    $this->resetPage();
}

public function updatedEventsListSearch(): void
{
    $this->resetPage();
}

/** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Event> */
public function getEventsUpcomingListProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->when($this->eventsListTab === 'upcoming', fn ($q) => $q->upcoming())
        ->when($this->eventsListTab === 'past', fn ($q) => $q->past())
        ->when($this->eventsListSearch, fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$this->eventsListSearch}%")->orWhere('venue_name', 'like', "%{$this->eventsListSearch}%")))
        ->orderBy('start_date', $this->eventsListTab === 'past' ? 'desc' : 'asc')
        ->paginate(10);
}
@endphp
--}}

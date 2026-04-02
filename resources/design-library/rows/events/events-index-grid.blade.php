{{--
@name Events - Searchable Index Grid
@description Paginated events card grid with search, designed for a standalone Events index page.
@sort 15
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Events"
        default-tag="h1"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-8" />

    {{-- Search --}}
    <x-dl.wrapper slug="__SLUG__" prefix="search_wrapper"
        default-classes="mb-8 relative max-w-sm">
        <div class="pointer-events-none absolute inset-y-0 inset-s-0 flex items-center ps-3 text-zinc-400/75">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
        </div>
        <x-dl.wrapper slug="__SLUG__" prefix="search_input" tag="input"
            wire:model.live.debounce.300ms="eventsIndexSearch"
            type="search"
            placeholder="Search events…"
            default-classes="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg bg-white text-base sm:text-sm py-2 h-10 ps-10 pe-3 text-zinc-700 placeholder-zinc-400 dark:bg-white/10 dark:border-white/10 dark:text-zinc-300 dark:placeholder-zinc-400" />
    </x-dl.wrapper>

    @if ($this->eventsIndexData->isEmpty())
        <x-dl.wrapper slug="__SLUG__" prefix="empty_message" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No upcoming events found.
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="events_grid"
            note="Content is pulled from the <a href='/dashboard/events' class='text-primary underline hover:text-primary/80'>Events</a> page."
            default-classes="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($this->eventsIndexData as $event)
                <x-dl.card slug="__SLUG__" prefix="event_card" tag="a"
                    wire:key="evindex-{{ $event->id }}"
                    href="{{ route('events.show', $event->slug) }}"
                    default-classes="group block bg-white dark:bg-zinc-900 rounded-card overflow-hidden shadow-card hover:shadow-md transition-shadow">
                    @if ($event->featured_image)
                        <x-dl.wrapper slug="__SLUG__" prefix="card_image" tag="img"
                            src="{{ \Illuminate\Support\Facades\Storage::url($event->featured_image) }}"
                            alt="{{ $event->title }}"
                            default-classes="w-full h-44 object-cover" />
                    @endif
                    <x-dl.group slug="__SLUG__" prefix="card_body"
                        default-classes="p-6">
                        <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h2"
                            default-classes="font-semibold text-base leading-snug mb-2 group-hover:text-zinc-500 dark:group-hover:text-zinc-400 transition-colors">
                            {{ $event->title }}
                        </x-dl.wrapper>
                        @if ($event->start_date)
                            <x-dl.wrapper slug="__SLUG__" prefix="card_date" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $event->start_date->format('M j, Y') }}
                            </x-dl.wrapper>
                        @endif
                        @if ($event->venue_name)
                            <x-dl.wrapper slug="__SLUG__" prefix="card_venue" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ $event->venue_name }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="pagination"
            default-classes="mt-10">
            {{ $this->eventsIndexData->links() }}
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
use \Livewire\WithPagination;

#[\Livewire\Attributes\Url(as: 'q')]
public string $eventsIndexSearch = '';

public function updatedEventsIndexSearch(): void
{
    $this->resetPage();
}

/** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Event> */
public function getEventsIndexDataProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
{
    return \App\Models\Event::query()
        ->published()
        ->upcoming()
        ->parent()
        ->when($this->eventsIndexSearch, fn ($q) => $q->where(function ($q): void {
            $q->where('title', 'like', "%{$this->eventsIndexSearch}%")
                ->orWhere('venue_name', 'like', "%{$this->eventsIndexSearch}%");
        }))
        ->orderBy('start_date')
        ->paginate(12);
}
@endphp
--}}

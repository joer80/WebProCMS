<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Browse upcoming and past events.'])] #[Title('Events')] class extends Component {
    public string $pageName = 'Events';

    // ROW:php:start:events-index:eVnTsX
    use \Livewire\WithPagination;

    #[\Livewire\Attributes\Url(as: 'q')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Event> */
    public function getEventsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Event::query()
            ->published()
            ->upcoming()
            ->parent()
            ->when($this->search, fn ($q) => $q->where(function ($q): void {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('venue_name', 'like', "%{$this->search}%");
            }))
            ->orderBy('start_date')
            ->paginate(12);
    }
    // ROW:php:end:events-index:eVnTsX
}; ?>
<div>
    {{-- ROW:start:events-index:eVnTsX --}}
    <x-dl.section slug="events-index:eVnTsX"
        default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
        default-container-classes="max-w-6xl mx-auto">
        <x-dl.heading slug="events-index:eVnTsX" prefix="headline" default="Events"
            default-tag="h1"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-6" />

        @if ($this->events->isEmpty())
            <x-dl.wrapper slug="events-index:eVnTsX" prefix="empty_message" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                No upcoming events found.
            </x-dl.wrapper>
        @else
            <x-dl.wrapper slug="events-index:eVnTsX" prefix="events_grid"
                default-classes="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->events as $event)
                    <x-dl.card slug="events-index:eVnTsX" prefix="event_card" tag="a"
                        wire:key="event-{{ $event->id }}"
                        href="{{ route('events.show', $event->slug) }}"
                        default-classes="group block bg-white dark:bg-zinc-900 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden hover:shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.3)] transition-shadow">
                            @if ($event->featured_image)
                            <x-dl.wrapper slug="events-index:eVnTsX" prefix="event_img" tag="img"
                                src="{{ $event->featuredImageUrl() }}"
                                alt="{{ $event->title }}"
                                default-classes="w-full h-44 object-cover" />
                        @endif
                        <x-dl.group slug="events-index:eVnTsX" prefix="card_body"
                            default-classes="p-6">
                            <x-dl.wrapper slug="events-index:eVnTsX" prefix="event_title" tag="h2"
                                default-classes="font-semibold text-base leading-snug mb-2 group-hover:text-zinc-500 dark:group-hover:text-zinc-400 transition-colors">
                                {{ $event->title }}
                            </x-dl.wrapper>
                            @if ($event->start_date)
                                <x-dl.wrapper slug="events-index:eVnTsX" prefix="event_date" tag="p"
                                    default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $event->start_date->format('M j, Y') }}
                                </x-dl.wrapper>
                            @endif
                            @if ($event->venue_name)
                                <x-dl.wrapper slug="events-index:eVnTsX" prefix="event_venue" tag="p"
                                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ $event->venue_name }}
                                </x-dl.wrapper>
                            @endif
                        </x-dl.group>
                    </x-dl.card>
                @endforeach
            </x-dl.wrapper>
            <x-dl.wrapper slug="events-index:eVnTsX" prefix="pagination"
                default-classes="mt-10">
                {{ $this->events->links() }}
            </x-dl.wrapper>
        @endif
    </x-dl.section>
    {{-- ROW:end:events-index:eVnTsX --}}
</div>

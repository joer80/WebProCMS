{{-- @previewContext model=\App\Models\Event label=title value=slug routeParam=slug orderBy=start_date --}}
<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    // ROW:php:start:event-detail:kPmWqZ
    public \App\Models\Event $event;

    public function mount(string $slug): void
    {
        $this->event = \App\Models\Event::query()
            ->accessible()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function title(): string
    {
        return $this->event->meta_title ?: ($this->event->title . ' — ' . config('app.name'));
    }
    // ROW:php:end:event-detail:kPmWqZ
}; ?>
<div>
    {{-- ROW:start:event-detail:kPmWqZ --}}
    <x-dl.section slug="event-detail:kPmWqZ"
        default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
        default-container-classes="max-w-4xl mx-auto">
        <x-dl.heading slug="event-detail:kPmWqZ" prefix="headline" default="Event Title"
            default-tag="h1"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        @if (isset($event) && $event->start_date)
            <x-dl.wrapper slug="event-detail:kPmWqZ" prefix="event_date" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 mb-2">
                {{ $event->start_date->format('F j, Y') }}
            </x-dl.wrapper>
        @endif
        @if (isset($event) && $event->venue_name)
            <x-dl.wrapper slug="event-detail:kPmWqZ" prefix="event_venue" tag="p"
                default-classes="text-zinc-500 dark:text-zinc-400 mb-6">
                {{ $event->venue_name }}
            </x-dl.wrapper>
        @endif
        @if (isset($event) && $event->content)
            <x-dl.wrapper slug="event-detail:kPmWqZ" prefix="event_content"
                default-classes="prose dark:prose-invert max-w-none">
                {!! $event->content !!}
            </x-dl.wrapper>
        @endif
    </x-dl.section>
    {{-- ROW:end:event-detail:kPmWqZ --}}
</div>

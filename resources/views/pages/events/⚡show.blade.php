<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    // ROW:php:start:event-article:placeholder
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

    public function getProcessedContentProperty(): string
    {
        return \App\Support\ShortcodeProcessor::processRaw($this->event->content ?? '');
    }

    public function getProcessedExcerptProperty(): ?string
    {
        return $this->event->excerpt ? \App\Support\ShortcodeProcessor::process($this->event->excerpt) : null;
    }

    public function getNextEventProperty(): ?\App\Models\Event
    {
        return \App\Models\Event::query()
            ->published()
            ->upcoming()
            ->where('start_date', '>', $this->event->start_date)
            ->orderBy('start_date')
            ->first();
    }
    // ROW:php:end:event-article:placeholder
}; ?>
<div>{{-- ROW:start:page-title-banner:event-show-banner --}}
<x-dl.section slug="page-title-banner:event-show-banner"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="page-title-banner:event-show-banner" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-black/50" />
    <x-dl.heading slug="page-title-banner:event-show-banner" prefix="headline" default="{{ $pageName ?? 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>

{{-- ROW:end:page-title-banner:event-show-banner --}}

{{-- ROW:start:event-article:placeholder --}}
@if(isset($event))
@push('head')
    <link rel="canonical" href="{{ route('events.show', $event->slug) }}" />

    @if ($event->meta_description || $event->excerpt)
        <meta name="description" content="{{ $event->meta_description ?? strip_tags($event->excerpt) }}" />
    @endif

    @if ($event->is_noindex)
        <meta name="robots" content="noindex, nofollow" />
    @endif

    <meta property="og:type" content="event" />
    <meta property="og:url" content="{{ route('events.show', $event->slug) }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    @if ($event->meta_description || $event->excerpt)
        <meta property="og:description" content="{{ $event->meta_description ?? strip_tags($event->excerpt) }}" />
        <meta name="twitter:description" content="{{ $event->meta_description ?? strip_tags($event->excerpt) }}" />
    @endif

    @php $ogImageUrl = $event->og_image ?: $event->featuredImageUrl() ?: \App\Models\Setting::get('seo.og.default_image', ''); @endphp
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    @if (\App\Models\Setting::get('seo.twitter.handle', ''))
        <meta name="twitter:site" content="{{ \App\Models\Setting::get('seo.twitter.handle', '') }}" />
    @endif
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}" />
    @endif

    @php
        $eventTimezone = $event->timezone ?: \App\Models\Setting::get('site.timezone', date_default_timezone_get());
        $eventSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'url' => route('events.show', $event->slug),
            'startDate' => $event->start_date->setTimezone($eventTimezone)->toIso8601String(),
        ];
        if ($event->end_date) {
            $eventSchema['endDate'] = $event->end_date->setTimezone($eventTimezone)->toIso8601String();
        }
        if ($event->meta_description || $event->excerpt) {
            $eventSchema['description'] = $event->meta_description ?? strip_tags($event->excerpt);
        }
        if ($event->featuredImageUrl()) {
            $eventSchema['image'] = $event->featuredImageUrl();
        }
        if ($event->venue_name || $event->venue_address) {
            $eventSchema['location'] = array_filter([
                '@type' => 'Place',
                'name' => $event->venue_name,
                'address' => $event->venue_address,
            ]);
        }
        if ($event->cost) {
            $eventSchema['offers'] = [
                '@type' => 'Offer',
                'price' => $event->cost,
            ];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@endif

<x-dl.section slug="event-article:placeholder"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">

    {{-- Breadcrumb --}}
    <x-dl.wrapper slug="event-article:placeholder" prefix="breadcrumb" tag="nav"
        default-classes="mb-8 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('events.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">Events</a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-zinc-100 truncate">{{ $event->title ?? 'Event' }}</span>
    </x-dl.wrapper>

    {{-- Article --}}
    <article>
        <x-dl.wrapper slug="event-article:placeholder" prefix="event_title" tag="h1"
            default-classes="text-4xl font-semibold leading-tight mb-4">
            {{ $event->title ?? 'Event Title' }}
        </x-dl.wrapper>

        @if (isset($event))
            {{-- Event Details Info Block --}}
            <x-dl.wrapper slug="event-article:placeholder" prefix="event_info"
                default-classes="mb-8 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 space-y-2 text-sm">
                @php
                    $displayTz = $event->timezone ?: \App\Models\Setting::get('site.timezone', date_default_timezone_get());
                    $startDate = $event->start_date->setTimezone($displayTz);
                    $endDate = $event->end_date?->setTimezone($displayTz);
                @endphp
                <x-dl.wrapper slug="event-article:placeholder" prefix="event_info_date"
                    default-classes="flex items-start gap-2 text-zinc-700 dark:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mt-0.5 shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" /></svg>
                    <span>
                        @if ($event->is_all_day)
                            {{ $startDate->format('l, F j, Y') }}
                            @if ($endDate)
                                – {{ $endDate->format('l, F j, Y') }}
                            @endif
                            <span class="ml-1 text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">All Day</span>
                        @else
                            {{ $startDate->format('l, F j, Y · g:i A') }}
                            @if ($endDate)
                                – {{ $endDate->format('g:i A') }}
                            @endif
                            <span class="ml-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $startDate->format('T') }}</span>
                        @endif
                    </span>
                </x-dl.wrapper>
                @if ($event->venue_name || $event->venue_address)
                    <x-dl.wrapper slug="event-article:placeholder" prefix="event_info_venue"
                        default-classes="flex items-start gap-2 text-zinc-700 dark:text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mt-0.5 shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        <span>
                            @if ($event->venue_name)
                                <strong>{{ $event->venue_name }}</strong>
                            @endif
                            @if ($event->venue_address)
                                <br />{{ $event->venue_address }}
                            @endif
                        </span>
                    </x-dl.wrapper>
                @endif
                @if ($event->website_url)
                    <x-dl.wrapper slug="event-article:placeholder" prefix="event_info_url"
                        default-classes="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                        <a href="{{ $event->website_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">{{ $event->website_url }}</a>
                    </x-dl.wrapper>
                @endif
                @if ($event->cost)
                    <x-dl.wrapper slug="event-article:placeholder" prefix="event_info_cost"
                        default-classes="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>{{ $event->cost }}</span>
                    </x-dl.wrapper>
                @endif
            </x-dl.wrapper>
        @endif

        @if (isset($event) && $event->featured_image)
            <x-dl.wrapper slug="event-article:placeholder" prefix="featured_image" tag="img"
                src="{{ $event->featuredImageUrl() }}"
                alt="{{ $event->featured_image_alt ?? $event->title }}"
                default-classes="w-full rounded-lg object-cover max-h-96 mb-8" />
        @endif

        @if (isset($event) && $event->excerpt)
            <x-dl.wrapper slug="event-article:placeholder" prefix="excerpt" tag="p"
                default-classes="text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8 border-l-2 border-zinc-200 dark:border-zinc-700 pl-4">
                {!! $this->processedExcerpt ?? '' !!}
            </x-dl.wrapper>
        @endif

        @if (isset($event) && $event->content)
            <x-dl.wrapper slug="event-article:placeholder" prefix="event_content"
                default-classes="leading-relaxed text-zinc-900 dark:text-zinc-100 blog-content">
                {!! $this->processedContent ?? '' !!}
            </x-dl.wrapper>
        @endif

        @if (isset($event) && $event->cta_buttons)
            <x-dl.wrapper slug="event-article:placeholder" prefix="cta_buttons"
                default-classes="mt-8 flex flex-wrap gap-3">
                @foreach ($event->cta_buttons as $button)
                    <x-dl.wrapper slug="event-article:placeholder" prefix="cta_button_link" tag="a"
                        href="{{ $button['url'] }}"
                        target="{{ $button['target'] ?? '_self' }}"
                        rel="{{ ($button['target'] ?? '_self') === '_blank' ? 'noopener noreferrer' : '' }}"
                        default-classes="inline-flex items-center px-6 py-3 bg-zinc-900 dark:bg-zinc-100 text-zinc-100 dark:text-zinc-900 rounded-lg font-medium hover:opacity-90 transition-opacity">
                        {{ $button['text'] }}
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>
        @endif
    </article>

    {{-- Gallery --}}
    @if (isset($event) && $event->gallery_images)
        <x-dl.wrapper slug="event-article:placeholder" prefix="gallery"
            default-classes="mt-10"
            x-data="{
                images: @js($event->galleryImagesData()),
                isOpen: false,
                currentIndex: 0,
                open(index) { this.currentIndex = index; this.isOpen = true; document.body.style.overflow = 'hidden'; },
                close() { this.isOpen = false; document.body.style.overflow = ''; },
                prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; },
                next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; }
            }"
            @keydown.escape.window="if (isOpen) close()"
            @keydown.arrow-left.window="if (isOpen) prev()"
            @keydown.arrow-right.window="if (isOpen) next()">
            <x-dl.wrapper slug="event-article:placeholder" prefix="gallery_grid" tag="div"
                default-classes="grid gap-3"
                style="grid-template-columns: repeat({{ $event->gallery_columns ?? 4 }}, minmax(0, 1fr))">
                @foreach ($event->galleryImagesData() as $index => $image)
                    <button type="button" @click="open({{ $index }})" class="aspect-square overflow-hidden rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:ring-offset-2">
                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?: 'Gallery photo ' . ($index + 1) }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-200" />
                    </button>
                @endforeach
            </x-dl.wrapper>
            {{-- Lightbox --}}
            <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/90" @click.self="close()">
                <button type="button" @click="close()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
                <button type="button" @click="prev()" x-show="images.length > 1" class="absolute left-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Previous photo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                </button>
                <div class="max-w-5xl max-h-screen w-full px-16 py-8 flex items-center justify-center">
                    <template x-for="(image, i) in images" :key="i">
                        <img x-show="currentIndex === i" :src="image.url" :alt="image.alt || `Gallery photo ${i + 1}`" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" />
                    </template>
                </div>
                <button type="button" @click="next()" x-show="images.length > 1" class="absolute right-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Next photo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </button>
                <div x-show="images.length > 1" x-text="`${currentIndex + 1} / ${images.length}`" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm tabular-nums"></div>
            </div>
        </x-dl.wrapper>
    @endif

    {{-- Event navigation --}}
    <x-dl.wrapper slug="event-article:placeholder" prefix="event_nav"
        default-classes="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
            Back to Events
        </a>
        @if (isset($this->nextEvent) && $this->nextEvent)
            <a href="{{ route('events.show', $this->nextEvent->slug) }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors text-end">
                <span class="truncate max-w-48">{{ $this->nextEvent->title }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </a>
        @endif
    </x-dl.wrapper>

</x-dl.section>
{{-- ROW:end:event-article:placeholder --}}
</div>

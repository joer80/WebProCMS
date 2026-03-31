{{--
@name Events - Featured Event
@description Highlights the next upcoming event prominently with additional upcoming events listed alongside.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Featured Event"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our next upcoming event."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    @if ($this->nextFeaturedEvent)
        <x-dl.wrapper slug="__SLUG__" prefix="featured_layout"
            default-classes="grid md:grid-cols-5 gap-8">
            {{-- LEFT: featured event (3 cols) --}}
            <x-dl.wrapper slug="__SLUG__" prefix="featured_main"
                default-classes="md:col-span-3">
                <a href="{{ route('events.show', $this->nextFeaturedEvent->slug) }}" class="block group">
                    {{-- Featured image --}}
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_image_wrapper"
                        default-classes="relative aspect-video rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 mb-4">
                        @if ($this->nextFeaturedEvent->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($this->nextFeaturedEvent->featured_image) }}"
                                alt="{{ $this->nextFeaturedEvent->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">No image</div>
                        @endif
                        {{-- Large date badge --}}
                        <x-dl.wrapper slug="__SLUG__" prefix="featured_date_badge"
                            default-classes="absolute top-4 left-4 flex flex-col items-center justify-center w-16 h-16 rounded-lg bg-primary text-white text-center shadow-lg">
                            <span class="text-xs font-semibold uppercase leading-none">{{ $this->nextFeaturedEvent->start_date->format('M') }}</span>
                            <span class="text-3xl font-bold leading-none">{{ $this->nextFeaturedEvent->start_date->format('j') }}</span>
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_date_meta" tag="p"
                        default-classes="text-sm text-primary font-semibold mb-2">
                        {{ $this->nextFeaturedEvent->start_date->format('l, F j, Y') }}
                        @if (! $this->nextFeaturedEvent->is_all_day)
                            · {{ $this->nextFeaturedEvent->start_date->format('g:i A') }}
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_title" tag="h2"
                        default-classes="text-2xl font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors mb-3">
                        {{ $this->nextFeaturedEvent->title }}
                    </x-dl.wrapper>
                    @if ($this->nextFeaturedEvent->excerpt)
                        <x-dl.wrapper slug="__SLUG__" prefix="featured_excerpt" tag="p"
                            default-classes="text-zinc-500 dark:text-zinc-400 mb-4">
                            {{ $this->nextFeaturedEvent->excerpt }}
                        </x-dl.wrapper>
                    @endif
                    <x-dl.wrapper slug="__SLUG__" prefix="featured_venue_meta" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                        @if ($this->nextFeaturedEvent->venue_name)
                            {{ $this->nextFeaturedEvent->venue_name }}
                        @endif
                        @if ($this->nextFeaturedEvent->cost)
                            · {{ $this->nextFeaturedEvent->cost }}
                        @endif
                    </x-dl.wrapper>
                </a>
            </x-dl.wrapper>

            {{-- RIGHT: more events sidebar (2 cols) --}}
            <x-dl.wrapper slug="__SLUG__" prefix="featured_sidebar"
                default-classes="md:col-span-2">
                <x-dl.wrapper slug="__SLUG__" prefix="sidebar_heading" tag="h3"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                    More Events
                </x-dl.wrapper>
                @if ($this->nextFeaturedOtherEvents->isEmpty())
                    <x-dl.wrapper slug="__SLUG__" prefix="sidebar_empty" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
                        No other upcoming events.
                    </x-dl.wrapper>
                @else
                    <x-dl.wrapper slug="__SLUG__" prefix="sidebar_list"
                        default-classes="space-y-4">
                        @foreach ($this->nextFeaturedOtherEvents as $otherEvent)
                            <x-dl.card slug="__SLUG__" prefix="sidebar_item" tag="a"
                                wire:key="efeat-other-{{ $otherEvent->id }}"
                                href="{{ route('events.show', $otherEvent->slug) }}"
                                default-classes="flex items-start gap-3 group">
                                <x-dl.group slug="__SLUG__" prefix="sidebar_date_badge"
                                    default-classes="flex flex-col items-center justify-center w-10 h-10 rounded bg-primary/10 text-primary shrink-0 text-center">
                                    <span class="text-xs font-semibold uppercase leading-none">{{ $otherEvent->start_date->format('M') }}</span>
                                    <span class="text-sm font-bold leading-none">{{ $otherEvent->start_date->format('j') }}</span>
                                </x-dl.group>
                                <x-dl.group slug="__SLUG__" prefix="sidebar_item_content"
                                    default-classes="flex-1 min-w-0">
                                    <x-dl.wrapper slug="__SLUG__" prefix="sidebar_item_title" tag="span"
                                        default-classes="block font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-primary transition-colors truncate text-sm">
                                        {{ $otherEvent->title }}
                                    </x-dl.wrapper>
                                    <x-dl.wrapper slug="__SLUG__" prefix="sidebar_item_time" tag="span"
                                        default-classes="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        @if ($otherEvent->is_all_day)
                                            All Day
                                        @else
                                            {{ $otherEvent->start_date->format('g:i A') }}
                                        @endif
                                    </x-dl.wrapper>
                                </x-dl.group>
                            </x-dl.card>
                        @endforeach
                    </x-dl.wrapper>
                @endif
            </x-dl.wrapper>
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="featured_empty" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            No upcoming events at this time.
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
/** @return \App\Models\Event|null */
public function getNextFeaturedEventProperty(): ?\App\Models\Event
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->upcoming()
        ->orderBy('start_date')
        ->first();
}

/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getNextFeaturedOtherEventsProperty(): \Illuminate\Database\Eloquent\Collection
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->upcoming()
        ->orderBy('start_date')
        ->skip(1)
        ->limit(4)
        ->get();
}
@endphp
--}}

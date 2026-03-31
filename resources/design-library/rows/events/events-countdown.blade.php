{{--
@name Events - Countdown
@description Highlights the next upcoming event with a live countdown timer. Great for hero or banner sections.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900 dark:bg-zinc-800"
    default-container-classes="max-w-4xl mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Don't Miss It"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our next event is coming up fast."
        default-classes="text-zinc-400 leading-normal mb-10" />

    @if ($this->countdownEventData)
        <x-dl.wrapper slug="__SLUG__" prefix="countdown_event_title" tag="h3"
            default-classes="text-2xl font-semibold text-white mb-3">
            {{ $this->countdownEventData->title }}
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="countdown_event_meta" tag="p"
            default-classes="text-zinc-400 text-sm mb-8">
            {{ $this->countdownEventData->start_date->format('l, F j, Y') }}
            @if (! $this->countdownEventData->is_all_day)
                · {{ $this->countdownEventData->start_date->format('g:i A') }}
            @endif
            @if ($this->countdownEventData->venue_name)
                · {{ $this->countdownEventData->venue_name }}
            @endif
            @if ($this->countdownEventData->cost)
                · {{ $this->countdownEventData->cost }}
            @endif
        </x-dl.wrapper>

        <x-dl.wrapper slug="__SLUG__" prefix="countdown_timer"
            :data-target="$this->countdownEventData->start_date->toIso8601String()"
            x-data="{ days: 0, hours: 0, minutes: 0, seconds: 0, init() { const target = new Date(this.$el.dataset.target); const tick = () => { const diff = target - Date.now(); if (diff < 1) { this.days = 0; this.hours = 0; this.minutes = 0; this.seconds = 0; return; } this.days = Math.floor(diff / 86400000); this.hours = Math.floor((diff % 86400000) / 3600000); this.minutes = Math.floor((diff % 3600000) / 60000); this.seconds = Math.floor((diff % 60000) / 1000); }; tick(); setInterval(tick, 1000); } }"
            default-classes="flex items-center justify-center gap-4 mt-6 mb-10">
            <x-dl.card slug="__SLUG__" prefix="countdown_days"
                default-classes="flex flex-col items-center justify-center w-20 h-20 rounded-lg bg-white/10 text-white">
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_days_num" tag="span"
                    x-text="days"
                    default-classes="text-3xl font-bold leading-none">
                    0
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_days_label" tag="span"
                    default-classes="text-xs text-zinc-400 mt-1 uppercase tracking-wide">
                    Days
                </x-dl.wrapper>
            </x-dl.card>
            <x-dl.card slug="__SLUG__" prefix="countdown_hours"
                default-classes="flex flex-col items-center justify-center w-20 h-20 rounded-lg bg-white/10 text-white">
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_hours_num" tag="span"
                    x-text="hours"
                    default-classes="text-3xl font-bold leading-none">
                    0
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_hours_label" tag="span"
                    default-classes="text-xs text-zinc-400 mt-1 uppercase tracking-wide">
                    Hours
                </x-dl.wrapper>
            </x-dl.card>
            <x-dl.card slug="__SLUG__" prefix="countdown_minutes"
                default-classes="flex flex-col items-center justify-center w-20 h-20 rounded-lg bg-white/10 text-white">
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_minutes_num" tag="span"
                    x-text="minutes"
                    default-classes="text-3xl font-bold leading-none">
                    0
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_minutes_label" tag="span"
                    default-classes="text-xs text-zinc-400 mt-1 uppercase tracking-wide">
                    Minutes
                </x-dl.wrapper>
            </x-dl.card>
            <x-dl.card slug="__SLUG__" prefix="countdown_seconds"
                default-classes="flex flex-col items-center justify-center w-20 h-20 rounded-lg bg-white/10 text-white">
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_seconds_num" tag="span"
                    x-text="seconds"
                    default-classes="text-3xl font-bold leading-none">
                    0
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="countdown_seconds_label" tag="span"
                    default-classes="text-xs text-zinc-400 mt-1 uppercase tracking-wide">
                    Seconds
                </x-dl.wrapper>
            </x-dl.card>
        </x-dl.wrapper>

        <x-dl.wrapper slug="__SLUG__" prefix="countdown_cta" tag="a"
            href="{{ route('events.show', $this->countdownEventData->slug) }}"
            default-classes="inline-block px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            View Event
        </x-dl.wrapper>
    @else
        <x-dl.wrapper slug="__SLUG__" prefix="countdown_empty" tag="p"
            default-classes="text-zinc-400 text-sm">
            No upcoming events scheduled.
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{--
@php
/** @return \App\Models\Event|null */
public function getCountdownEventDataProperty(): ?\App\Models\Event
{
    return \App\Models\Event::query()
        ->published()
        ->parent()
        ->upcoming()
        ->orderBy('start_date')
        ->first();
}
@endphp
--}}

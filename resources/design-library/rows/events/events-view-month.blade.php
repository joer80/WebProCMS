{{--
@name Events - Month Calendar
@description Month calendar view only with prev/next navigation. Shows events on their dates.
@sort 15
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Events Calendar"
        default-tag="h1"
        default-classes="text-4xl font-semibold leading-tight mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Browse events by month."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    @php
        $calMonthStr = $eventsCalMonth ?: now()->format('Y-m');
        $monthCal = \Carbon\Carbon::createFromFormat('Y-m', $calMonthStr)->startOfMonth();
        $eventsCalData = $this->eventsCalMonthData->map(fn ($e) => [
            'title' => $e->title,
            'slug' => $e->slug,
            'start' => $e->start_date->format('Y-m-d'),
        ])->values()->toArray();
        $todayCal = now()->format('Y-m-d');
        $startCal = $monthCal->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $calCells = [];
        for ($i = 0; $i < 42; $i++) {
            $calCells[] = $startCal->copy()->addDays($i);
        }
        $calEventsByDate = collect($eventsCalData)->groupBy('start');
    @endphp

    {{-- Month header: title left, nav right --}}
    <x-dl.wrapper slug="__SLUG__" prefix="cal_month_header"
        default-classes="flex items-center justify-between mb-4">
        <x-dl.wrapper slug="__SLUG__" prefix="cal_month_title" tag="h2"
            default-classes="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
            {{ $monthCal->format('F Y') }}
        </x-dl.wrapper>
        <x-dl.group slug="__SLUG__" prefix="cal_month_nav"
            default-classes="flex items-center gap-2">
            <x-dl.wrapper slug="__SLUG__" prefix="cal_prev_btn" tag="button"
                wire:click="previousEventsCalMonth"
                default-classes="p-1.5 rounded border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-zinc-600 dark:text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="cal_next_btn" tag="button"
                wire:click="nextEventsCalMonth"
                default-classes="p-1.5 rounded border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-zinc-600 dark:text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </x-dl.wrapper>
        </x-dl.group>
    </x-dl.wrapper>

    {{-- Day-of-week headers --}}
    <x-dl.wrapper slug="__SLUG__" prefix="cal_dow_headers"
        default-classes="grid grid-cols-7 mb-1">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dowName)
            <x-dl.wrapper slug="__SLUG__" prefix="cal_dow_header" tag="div"
                default-classes="text-xs font-medium text-zinc-500 dark:text-zinc-400 text-center py-2">
                {{ $dowName }}
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>

    {{-- Calendar grid --}}
    <x-dl.wrapper slug="__SLUG__" prefix="cal_grid"
        default-classes="grid grid-cols-7 border-l border-t border-zinc-200 dark:border-zinc-700">
        @foreach ($calCells as $calCell)
            @php
                $calCellDate = $calCell->format('Y-m-d');
                $calCellCurrentMonth = $calCell->month === $monthCal->month;
                $calCellIsToday = $calCellDate === $todayCal;
                $calCellEvents = $calEventsByDate[$calCellDate] ?? collect();
            @endphp
            <x-dl.wrapper slug="__SLUG__" prefix="cal_cell" tag="div"
                default-classes="min-h-24 p-1 border-r border-b border-zinc-200 dark:border-zinc-700 {{ $calCellCurrentMonth ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-50 dark:bg-zinc-800/30' }}">
                <x-dl.wrapper slug="__SLUG__" prefix="cal_cell_num" tag="span"
                    default-classes="inline-flex items-center justify-center size-6 text-xs font-medium mb-1 {{ $calCellIsToday ? 'rounded-full bg-primary text-white' : ($calCellCurrentMonth ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-600') }}">
                    {{ $calCell->day }}
                </x-dl.wrapper>
                @foreach ($calCellEvents as $ev)
                    <x-dl.wrapper slug="__SLUG__" prefix="cal_event_label" tag="a"
                        href="{{ route('events.show', $ev['slug']) }}"
                        default-classes="block truncate text-xs px-1 py-0.5 rounded bg-primary/10 text-primary hover:bg-primary/20 transition-colors mb-0.5">
                        {{ $ev['title'] }}
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>
{{--
@php
#[\Livewire\Attributes\Url(as: 'month')]
public string $eventsCalMonth = '';

public function previousEventsCalMonth(): void
{
    $month = $this->eventsCalMonth ?: now()->format('Y-m');
    $this->eventsCalMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');
}

public function nextEventsCalMonth(): void
{
    $month = $this->eventsCalMonth ?: now()->format('Y-m');
    $this->eventsCalMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->addMonth()->format('Y-m');
}

/** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> */
public function getEventsCalMonthDataProperty(): \Illuminate\Database\Eloquent\Collection
{
    $month = $this->eventsCalMonth ?: now()->format('Y-m');
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
@endphp
--}}

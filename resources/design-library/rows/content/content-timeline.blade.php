{{--
@name Milestone Timeline
@description Vertical timeline with year, title, and description for milestones.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Journey"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="From a small idea to a global platform."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="events"
        default-grid-classes="relative space-y-8 pl-12 before:absolute before:inset-y-0 before:left-4 before:w-0.5 before:bg-zinc-200 dark:before:bg-zinc-700"
        default-items='[{"year":"2020","title":"Founded","desc":"Two friends started building in a garage with a shared vision of simpler software."},{"year":"2021","title":"Launched Beta","desc":"Our first 100 customers helped shape the product into what it is today."},{"year":"2022","title":"Series A","desc":"Raised $10M to expand the team and accelerate product development."},{"year":"2023","title":"10K Customers","desc":"Crossed 10,000 active customers across 50 countries worldwide."},{"year":"2024","title":"Enterprise Edition","desc":"Launched our enterprise tier with dedicated SLAs and custom integrations."}]'>
        @dlItems('__SLUG__', 'events', $events, '[{"year":"2020","title":"Founded","desc":"Two friends started building in a garage with a shared vision of simpler software."},{"year":"2021","title":"Launched Beta","desc":"Our first 100 customers helped shape the product into what it is today."},{"year":"2022","title":"Series A","desc":"Raised $10M to expand the team and accelerate product development."},{"year":"2023","title":"10K Customers","desc":"Crossed 10,000 active customers across 50 countries worldwide."},{"year":"2024","title":"Enterprise Edition","desc":"Launched our enterprise tier with dedicated SLAs and custom integrations."}]')
        @foreach ($events as $event)
            <x-dl.card slug="__SLUG__" prefix="event_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="relative">
                <x-dl.wrapper slug="__SLUG__" prefix="event_dot"
                    default-classes="absolute -left-[2.25rem] top-1 size-4 rounded-full bg-primary border-2 border-white dark:border-zinc-900">
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="event_year" tag="span"
                    default-classes="text-xs font-bold text-primary uppercase tracking-wider">
                    {{ $event['year'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="event_title" tag="h3"
                    default-classes="mt-1 font-semibold text-zinc-900 dark:text-white">
                    {{ $event['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="event_desc" tag="p"
                    default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    {{ $event['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

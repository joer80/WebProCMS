{{--
@name Before & After
@description Side-by-side before and after comparison panels.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Before and After"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="See the difference our platform makes in your day-to-day operations."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="panels_grid"
        default-classes="grid md:grid-cols-2 gap-6">
        <x-dl.wrapper slug="__SLUG__" prefix="before_panel"
            default-classes="p-8 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
            <x-dl.wrapper slug="__SLUG__" prefix="before_heading" tag="h3"
                default-classes="font-bold text-zinc-500 text-sm uppercase tracking-wider mb-6">
                Before
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="before_items"
                default-grid-classes="space-y-3"
                default-items='[{"text":"Juggling 5+ disconnected tools"},{"text":"Hours lost to manual reporting"},{"text":"No visibility across teams"},{"text":"Slow, painful onboarding process"},{"text":"Missing deadlines constantly"}]'>
                @dlItems('__SLUG__', 'before_items', $beforeItems, '[{"text":"Juggling 5+ disconnected tools"},{"text":"Hours lost to manual reporting"},{"text":"No visibility across teams"},{"text":"Slow, painful onboarding process"},{"text":"Missing deadlines constantly"}]')
                @foreach ($beforeItems as $item)
                    <x-dl.card slug="__SLUG__" prefix="before_item"
                        default-classes="flex items-center gap-3 text-zinc-500 dark:text-zinc-400">
                        <x-dl.icon slug="__SLUG__" prefix="before_icon" name="x-mark"
                            default-classes="size-4 shrink-0 text-zinc-400" />
                        <x-dl.wrapper slug="__SLUG__" prefix="before_text" tag="span"
                            default-classes="text-sm">
                            {{ $item['text'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="after_panel"
            default-classes="p-8 rounded-card bg-primary/5 border-2 border-primary">
            <x-dl.wrapper slug="__SLUG__" prefix="after_heading" tag="h3"
                default-classes="font-bold text-primary text-sm uppercase tracking-wider mb-6">
                After
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="after_items"
                default-grid-classes="space-y-3"
                default-items='[{"text":"Everything in one unified platform"},{"text":"Automated reports in seconds"},{"text":"Real-time team visibility"},{"text":"Onboard new members in minutes"},{"text":"Hitting goals ahead of schedule"}]'>
                @dlItems('__SLUG__', 'after_items', $afterItems, '[{"text":"Everything in one unified platform"},{"text":"Automated reports in seconds"},{"text":"Real-time team visibility"},{"text":"Onboard new members in minutes"},{"text":"Hitting goals ahead of schedule"}]')
                @foreach ($afterItems as $item)
                    <x-dl.card slug="__SLUG__" prefix="after_item"
                        default-classes="flex items-center gap-3">
                        <x-dl.icon slug="__SLUG__" prefix="after_icon" name="check-circle"
                            default-classes="size-4 shrink-0 text-primary" />
                        <x-dl.wrapper slug="__SLUG__" prefix="after_text" tag="span"
                            default-classes="text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            {{ $item['text'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

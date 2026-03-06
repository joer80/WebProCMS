{{--
@name Features - Two Column
@description Two-column layout with image on one side and feature list on the other.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="A Better Way to Work"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our platform streamlines your entire workflow from day one."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
        <x-dl.grid slug="__SLUG__" prefix="features"
            default-grid-classes="mt-8 space-y-5"
            default-items='[{"icon":"check-circle","title":"Simple Setup","desc":"Up and running in minutes, not days."},{"icon":"check-circle","title":"Powerful Automation","desc":"Automate repetitive tasks and focus on what matters."},{"icon":"check-circle","title":"Team Collaboration","desc":"Work together seamlessly across teams and time zones."},{"icon":"check-circle","title":"Advanced Reporting","desc":"Make data-driven decisions with real-time dashboards."}]'>
            @dlItems('__SLUG__', 'features', $features, '[{"icon":"check-circle","title":"Simple Setup","desc":"Up and running in minutes, not days."},{"icon":"check-circle","title":"Powerful Automation","desc":"Automate repetitive tasks and focus on what matters."},{"icon":"check-circle","title":"Team Collaboration","desc":"Work together seamlessly across teams and time zones."},{"icon":"check-circle","title":"Advanced Reporting","desc":"Make data-driven decisions with real-time dashboards."}]')
            @foreach ($features as $feature)
                <x-dl.card slug="__SLUG__" prefix="feature_item"
                    default-classes="flex items-start gap-3">
                    <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                        default-classes="size-5 shrink-0 mt-0.5 text-primary" />
                    <div>
                        <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="span"
                            default-classes="font-semibold text-zinc-900 dark:text-white">
                            {{ $feature['title'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                            default-classes="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $feature['desc'] }}
                        </x-dl.wrapper>
                    </div>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </div>
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800"
        default-image-classes="w-full h-full object-cover"
        default-image="https://placehold.co/1200x675" />
</x-dl.section>

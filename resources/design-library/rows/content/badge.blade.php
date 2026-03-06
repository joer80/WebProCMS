{{--
@name Content - Badge
@description A small badge or label, ideal for highlighting announcements or section labels.
@sort 5
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto flex">
    <x-dl.badge slug="__SLUG__" prefix="badge"
        default-label="Now in Beta"
        default-classes="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full" />
</x-dl.section>

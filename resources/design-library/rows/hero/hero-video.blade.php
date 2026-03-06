{{--
@name Hero - Video
@description Two-column hero with text on the left and an embedded video on the right.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="See It In Action"
            default-tag="h1"
            default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Watch our quick demo and see how easy it is to get started today."
            default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="View Docs"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </div>
    <x-dl.video slug="__SLUG__" prefix="demo_video"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video"
        default-video-classes="w-full h-full"
        default-video-url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" />
</x-dl.section>

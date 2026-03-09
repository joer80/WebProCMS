{{--
@name Video Embed
@description Video embed section with heading and subheadline.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="See It in Action"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Watch a quick overview of how our platform works."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.video slug="__SLUG__" prefix="main_video"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video shadow-card"
        default-video-classes="w-full h-full"
        default-video-url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" />
</x-dl.section>

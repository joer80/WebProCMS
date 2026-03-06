{{--
@name Social Proof - Video Testimonial
@description Video testimonial embed with customer details below.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-10">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Hear From Our Customers"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Real stories from the teams building with us every day."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.video slug="__SLUG__" prefix="testimonial_video"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video shadow-card"
        default-video-classes="w-full h-full"
        default-video-url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" />
    <x-dl.wrapper slug="__SLUG__" prefix="customer_info"
        default-classes="mt-6 text-center">
        <x-dl.wrapper slug="__SLUG__" prefix="customer_name"
            default-classes="font-semibold text-zinc-900 dark:text-white">
            Jane Smith
        </x-dl.wrapper>
        <x-dl.subheadline slug="__SLUG__" prefix="customer_role" default="COO at Acme Corporation"
            default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1" />
    </x-dl.wrapper>
</x-dl.section>

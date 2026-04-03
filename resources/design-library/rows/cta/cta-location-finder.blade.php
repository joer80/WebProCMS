{{--
@name CTA - Location Finder
@description Split call-to-action with heading and text on the left and buttons aligned to the right.
@sort 110
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-banner px-6 bg-primary"
    default-container-classes="max-w-container mx-auto grid md:grid-cols-2 gap-8 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Find a Location Near You"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="With locations across the region, quality care is always close by. Walk in today or book online."
            default-classes="mt-3 text-white/80" />
    </div>
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="flex flex-wrap items-center justify-end gap-4"
        default-primary-label="Our Locations"
        default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
        default-secondary-label="Contact Us"
        default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>

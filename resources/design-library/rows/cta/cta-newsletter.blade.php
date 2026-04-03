{{--
@name CTA - Newsletter
@description Two-column call-to-action with text on the left and an email signup field on the right.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-banner px-6 bg-primary"
    default-container-classes="max-w-container mx-auto grid md:grid-cols-2 gap-10 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Stay in the Loop"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Get the latest updates delivered to your inbox."
            default-classes="mt-3 text-white/80" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="form_row"
        default-classes="flex flex-col sm:flex-row gap-3">
        <x-dl.wrapper slug="__SLUG__" prefix="email_input" tag="input" type="email" name="email"
            placeholder="Enter your email"
            default-classes="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30" />
        <x-dl.wrapper slug="__SLUG__" prefix="submit_button" tag="button" type="button"
            default-classes="px-6 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors whitespace-nowrap">
            Subscribe
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

{{--
@name Contact - Newsletter
@description Email subscription form with headline and brief description.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-primary"
    default-container-classes="max-w-3xl mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Stay in the Loop"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Subscribe to our newsletter for product updates, tips, and exclusive offers. No spam, ever."
        default-classes="text-white/80 mb-8" />
    <x-dl.wrapper slug="__SLUG__" prefix="form_row"
        default-classes="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
            type="email" placeholder="Enter your email"
            default-classes="flex-1 rounded-lg px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white/40" />
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="px-6 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors shrink-0">
            Subscribe
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="privacy_note" tag="p"
        default-classes="mt-4 text-xs text-white/60">
        By subscribing you agree to our Privacy Policy. Unsubscribe at any time.
    </x-dl.wrapper>
</x-dl.section>

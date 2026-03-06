{{--
@name Blog Detail - Newsletter CTA
@description Newsletter subscription call-to-action within a blog post.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="cta_card"
        default-classes="p-8 rounded-card bg-primary/5 dark:bg-primary/10 border border-primary/20 text-center">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Enjoying this article?"
            default-tag="h3"
            default-classes="font-heading text-2xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Subscribe to our newsletter for more insights, tutorials, and updates delivered straight to your inbox."
            default-classes="mt-3 text-zinc-600 dark:text-zinc-400" />
        <x-dl.wrapper slug="__SLUG__" prefix="email_form" tag="form"
            default-classes="mt-6 flex flex-col sm:flex-row gap-3 max-w-sm mx-auto">
            <x-dl.wrapper slug="__SLUG__" prefix="email_input" tag="input"
                type="email"
                placeholder="Enter your email"
                default-classes="flex-1 px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
            <x-dl.wrapper slug="__SLUG__" prefix="submit_button" tag="button"
                type="submit"
                default-classes="px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors whitespace-nowrap">
                Subscribe
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.subheadline slug="__SLUG__" prefix="privacy_note" tag="p" default="No spam, ever. Unsubscribe at any time."
            default-classes="mt-3 text-xs text-zinc-400 dark:text-zinc-500" />
    </x-dl.wrapper>
</x-dl.section>

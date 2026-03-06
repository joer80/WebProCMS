{{--
@name Contact - Centered
@description Centered contact form with heading above.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-2xl mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Get in Touch"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="We'd love to hear from you. Fill out the form and we'll be in touch shortly."
        default-classes="text-lg text-zinc-500 dark:text-zinc-400 mb-10" />
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="bg-white dark:bg-zinc-900 rounded-card border border-zinc-200 dark:border-zinc-700 p-8 text-left">
        <x-dl.wrapper slug="__SLUG__" prefix="form_grid"
            default-classes="grid md:grid-cols-2 gap-6 mb-6">
            <x-dl.wrapper slug="__SLUG__" prefix="field_name"
                default-classes="flex flex-col gap-1.5">
                <x-dl.wrapper slug="__SLUG__" prefix="label_name" tag="label"
                    default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                    type="text" placeholder="Your name"
                    default-classes="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_email"
                default-classes="flex flex-col gap-1.5">
                <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
                    default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Email
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                    type="email" placeholder="you@example.com"
                    default-classes="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_message"
            default-classes="flex flex-col gap-1.5 mb-6">
            <x-dl.wrapper slug="__SLUG__" prefix="label_message" tag="label"
                default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Message
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
                rows="5" placeholder="How can we help?"
                default-classes="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Send Message
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

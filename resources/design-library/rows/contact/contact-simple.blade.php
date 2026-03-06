{{--
@name Contact - Simple
@description Simple single-column contact form with no frills.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-lg mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Contact"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.wrapper slug="__SLUG__" prefix="form_wrapper"
        default-classes="space-y-5">
        <x-dl.wrapper slug="__SLUG__" prefix="field_name"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                type="text" placeholder="Name"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="Email"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_message"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
                rows="6" placeholder="Your message"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Send
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

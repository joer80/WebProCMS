{{--
@name Contact - Minimal
@description Minimalist contact form with no card wrapper, clean layout.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Send Us a Message"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-2" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="We typically respond within one business day."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-10" />
    <x-dl.wrapper slug="__SLUG__" prefix="field_name"
        default-classes="mb-5">
        <x-dl.wrapper slug="__SLUG__" prefix="label_name" tag="label"
            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Full Name
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
            type="text" placeholder="Jane Smith"
            default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="field_email"
        default-classes="mb-5">
        <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Email Address
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
            type="email" placeholder="jane@example.com"
            default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="field_subject"
        default-classes="mb-5">
        <x-dl.wrapper slug="__SLUG__" prefix="label_subject" tag="label"
            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Subject
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="input_subject" tag="input"
            type="text" placeholder="What's this about?"
            default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="field_message"
        default-classes="mb-8">
        <x-dl.wrapper slug="__SLUG__" prefix="label_message" tag="label"
            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
            Message
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
            rows="5" placeholder="Tell us more…"
            default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
        type="submit"
        default-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
        Send Message
    </x-dl.wrapper>
</x-dl.section>

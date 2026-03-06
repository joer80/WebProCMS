{{--
@name Contact - Split Image
@description Two-column contact with decorative image on the left.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="bg-white dark:bg-zinc-900"
    default-container-classes="">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 min-h-[600px]">
        <x-dl.image slug="__SLUG__" prefix="side_image"
            default-wrapper-classes="hidden md:block overflow-hidden"
            default-image-classes="w-full h-full object-cover" />
        <x-dl.wrapper slug="__SLUG__" prefix="form_panel"
            default-classes="px-8 md:px-16 py-16 flex flex-col justify-center">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Get in Touch"
                default-tag="h2"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-4" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Fill out the form and we'll get back to you within one business day."
                default-classes="text-zinc-500 dark:text-zinc-400 mb-8" />
            <x-dl.wrapper slug="__SLUG__" prefix="field_name"
                default-classes="mb-4">
                <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                    type="text" placeholder="Your name"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_email"
                default-classes="mb-4">
                <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                    type="email" placeholder="Email address"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_message"
                default-classes="mb-6">
                <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
                    rows="5" placeholder="How can we help?"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
                type="submit"
                default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Send Message
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

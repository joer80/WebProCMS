{{--
@name Contact - Support
@description Support-oriented contact page with FAQs link and tiered options.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="How Can We Help?"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Search our docs, browse FAQs, or contact support directly."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="support_options"
        default-grid-classes="grid md:grid-cols-3 gap-6 mb-12"
        default-items='[{"icon":"book-open","title":"Documentation","desc":"Browse our detailed guides and API references.","cta":"View Docs","link":"/docs"},{"icon":"chat-bubble-left-right","title":"Live Chat","desc":"Chat with our support team in real time.","cta":"Start Chat","link":"#"},{"icon":"envelope","title":"Email Support","desc":"Send us a message and we'll respond within 24 hours.","cta":"Send Email","link":"mailto:support@example.com"}]'>
        @dlItems('__SLUG__', 'support_options', $supportOptions, '[{"icon":"book-open","title":"Documentation","desc":"Browse our detailed guides and API references.","cta":"View Docs","link":"/docs"},{"icon":"chat-bubble-left-right","title":"Live Chat","desc":"Chat with our support team in real time.","cta":"Start Chat","link":"#"},{"icon":"envelope","title":"Email Support","desc":"Send us a message and we will respond within 24 hours.","cta":"Send Email","link":"mailto:support@example.com"}]')
        @foreach ($supportOptions as $option)
            <x-dl.card slug="__SLUG__" prefix="option_card"
                default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-6">
                <x-dl.icon slug="__SLUG__" prefix="option_icon" name="{{ $option['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-7" />
                <x-dl.wrapper slug="__SLUG__" prefix="option_title" tag="h3"
                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $option['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="option_desc" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-4">
                    {{ $option['desc'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="option_cta" tag="a"
                    href="{{ $option['link'] }}"
                    default-classes="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                    {{ $option['cta'] }} →
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-8 bg-zinc-50 dark:bg-zinc-800">
        <x-dl.wrapper slug="__SLUG__" prefix="form_title" tag="h3"
            default-classes="text-xl font-bold text-zinc-900 dark:text-white mb-6">
            Submit a Support Ticket
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="form_grid"
            default-classes="grid md:grid-cols-2 gap-5 mb-5">
            <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                type="text" placeholder="Your name"
                default-classes="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="Email address"
                default-classes="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_message"
            default-classes="mb-5">
            <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
                rows="4" placeholder="Describe the issue…"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Submit Ticket
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

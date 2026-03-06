{{--
@name Contact - Info Cards
@description Grid of contact method cards with icons and details.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Contact Us"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Choose the best way to reach us."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="channels"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"icon":"envelope","title":"Email Us","desc":"For general inquiries and support.","value":"hello@example.com","link":"mailto:hello@example.com"},{"icon":"phone","title":"Call Us","desc":"Mon–Fri, 9am–5pm EST.","value":"+1 (555) 123-4567","link":"tel:+15551234567"},{"icon":"map-pin","title":"Visit Us","desc":"Come say hello in person.","value":"123 Main St, San Francisco","link":"#"}]'>
        @dlItems('__SLUG__', 'channels', $channels, '[{"icon":"envelope","title":"Email Us","desc":"For general inquiries and support.","value":"hello@example.com","link":"mailto:hello@example.com"},{"icon":"phone","title":"Call Us","desc":"Mon–Fri, 9am–5pm EST.","value":"+1 (555) 123-4567","link":"tel:+15551234567"},{"icon":"map-pin","title":"Visit Us","desc":"Come say hello in person.","value":"123 Main St, San Francisco","link":"#"}]')
        @foreach ($channels as $channel)
            <x-dl.card slug="__SLUG__" prefix="channel_card"
                default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-6 text-center hover:border-primary transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="channel_icon" name="{{ $channel['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-8 mx-auto" />
                <x-dl.wrapper slug="__SLUG__" prefix="channel_title" tag="h3"
                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-1">
                    {{ $channel['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="channel_desc" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-3">
                    {{ $channel['desc'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="channel_value" tag="a"
                    href="{{ $channel['link'] }}"
                    default-classes="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                    {{ $channel['value'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

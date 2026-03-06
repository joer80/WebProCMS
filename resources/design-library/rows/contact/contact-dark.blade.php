{{--
@name Contact - Dark
@description Dark background contact section with form and info.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-start">
        <x-dl.wrapper slug="__SLUG__" prefix="left_panel"
            default-classes="">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Let's Talk"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-white mb-4" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Reach out and a member of our team will get back to you within 24 hours."
                default-classes="text-zinc-400 mb-8" />
            <x-dl.grid slug="__SLUG__" prefix="contact_info"
                default-grid-classes="space-y-4"
                default-items='[{"icon":"envelope","label":"Email","value":"hello@example.com"},{"icon":"phone","label":"Phone","value":"+1 (555) 123-4567"},{"icon":"map-pin","label":"Office","value":"123 Main St, San Francisco, CA"}]'>
                @dlItems('__SLUG__', 'contact_info', $contactInfo, '[{"icon":"envelope","label":"Email","value":"hello@example.com"},{"icon":"phone","label":"Phone","value":"+1 (555) 123-4567"},{"icon":"map-pin","label":"Office","value":"123 Main St, San Francisco, CA"}]')
                @foreach ($contactInfo as $item)
                    <x-dl.card slug="__SLUG__" prefix="info_item"
                        default-classes="flex items-start gap-3">
                        <x-dl.icon slug="__SLUG__" prefix="info_icon" name="{{ $item['icon'] }}"
                            default-classes="size-5 text-primary mt-0.5 shrink-0" />
                        <x-dl.group slug="__SLUG__" prefix="info_text"
                            default-classes="">
                            <x-dl.wrapper slug="__SLUG__" prefix="info_label" tag="span"
                                default-classes="block text-xs uppercase tracking-widest text-zinc-500 mb-0.5">
                                {{ $item['label'] }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="info_value" tag="span"
                                default-classes="text-zinc-200 text-sm">
                                {{ $item['value'] }}
                            </x-dl.wrapper>
                        </x-dl.group>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="form_panel"
            default-classes="bg-zinc-800 rounded-card p-8 border border-zinc-700">
            <x-dl.wrapper slug="__SLUG__" prefix="field_name"
                default-classes="mb-5">
                <x-dl.wrapper slug="__SLUG__" prefix="label_name" tag="label"
                    default-classes="block text-sm font-medium text-zinc-300 mb-1.5">
                    Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                    type="text" placeholder="Your name"
                    default-classes="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_email"
                default-classes="mb-5">
                <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
                    default-classes="block text-sm font-medium text-zinc-300 mb-1.5">
                    Email
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                    type="email" placeholder="you@example.com"
                    default-classes="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_message"
                default-classes="mb-6">
                <x-dl.wrapper slug="__SLUG__" prefix="label_message" tag="label"
                    default-classes="block text-sm font-medium text-zinc-300 mb-1.5">
                    Message
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="textarea_message" tag="textarea"
                    rows="5" placeholder="How can we help?"
                    default-classes="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
                type="submit"
                default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Send Message
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

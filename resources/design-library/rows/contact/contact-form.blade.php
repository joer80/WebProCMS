{{--
@name Contact - Form
@description Two-column contact section with info on the left and a form card on the right.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto grid lg:grid-cols-2 gap-12 items-start">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Get in Touch"
            default-tag="h1"
            default-classes="text-4xl font-semibold leading-tight mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="description" default="Have a question or just want to say hello? Fill out the form and we'll get back to you within one business day."
            default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-8" />
        <x-dl.grid slug="__SLUG__" prefix="contact_items"
            default-grid-classes="space-y-6 text-sm"
            default-items='[{"label":"Email","value":"hello@example.com"},{"label":"Phone","value":"(555) 000-0000"},{"label":"Office","value":"123 Main Street, San Francisco CA 94105"},{"label":"Hours","value":"Mon\u2013Fri: 9am\u20136pm"}]'>
            @dlItems('__SLUG__', 'contact_items', $contactItems, '[{"label":"Email","value":"hello@example.com"},{"label":"Phone","value":"(555) 000-0000"},{"label":"Office","value":"123 Main Street, San Francisco CA 94105"},{"label":"Hours","value":"Mon\u2013Fri: 9am\u20136pm"}]')
            @foreach ($contactItems as $contactItem)
                <x-dl.card slug="__SLUG__" prefix="contact_item" default-classes="">
                    <x-dl.wrapper slug="__SLUG__" prefix="item_label" tag="p"
                        default-classes="font-semibold mb-1">{{ $contactItem['label'] }}</x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="item_value" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400">{{ $contactItem['value'] }}</x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </div>
    <x-dl.group slug="__SLUG__" prefix="form" tag="form"
        default-classes="space-y-5 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
        <x-dl.card slug="__SLUG__" prefix="name_grid"
            default-classes="grid grid-cols-2 gap-4">
            <div>
                <x-dl.wrapper slug="__SLUG__" prefix="first_name_label" tag="label"
                    default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    First Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="first_name_input" tag="input"
                    type="text" placeholder="Jane"
                    default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>
            <div>
                <x-dl.wrapper slug="__SLUG__" prefix="last_name_label" tag="label"
                    default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Last Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="last_name_input" tag="input"
                    type="text" placeholder="Smith"
                    default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>
        </x-dl.card>
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="email_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Email
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="email_input" tag="input"
                type="email" placeholder="jane@example.com"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
        </div>
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="phone_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Phone Number
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="phone_input" tag="input"
                type="tel" placeholder="+1 (555) 000-0000"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
        </div>
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="inquiry_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Your Inquiry
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="inquiry_textarea" tag="textarea"
                rows="5" placeholder="Tell us how we can help…"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary resize-none">
            </x-dl.wrapper>
        </div>
        <x-dl.button slug="__SLUG__" prefix="submit" type="submit" default="Send Message"
            default-classes="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.group>
</x-dl.section>

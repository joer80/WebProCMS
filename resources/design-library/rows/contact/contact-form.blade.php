{{--
@name Contact - Form
@description Contact section with form fields and contact details sidebar.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
        <x-dl-wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-16">
            <x-dl-heading slug="__SLUG__" prefix="headline" default="Get in Touch"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="We'd love to hear from you. Send us a message and we'll respond as soon as possible."
                default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        </x-dl-wrapper>
        <x-dl-wrapper slug="__SLUG__" prefix="layout"
            default-classes="grid md:grid-cols-2 gap-12">
            <x-dl-wrapper slug="__SLUG__" prefix="form" tag="form"
                default-classes="space-y-6">
                <x-dl-wrapper slug="__SLUG__" prefix="name_grid"
                    default-classes="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-dl-wrapper slug="__SLUG__" prefix="label" tag="label"
                            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            First Name
                        </x-dl-wrapper>
                        <x-dl-wrapper slug="__SLUG__" prefix="input" tag="input"
                            type="text"
                            default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                    <div>
                        <x-dl-wrapper slug="__SLUG__" prefix="label" tag="label"
                            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Last Name
                        </x-dl-wrapper>
                        <x-dl-wrapper slug="__SLUG__" prefix="input" tag="input"
                            type="text"
                            default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                </x-dl-wrapper>
                <div>
                    <x-dl-wrapper slug="__SLUG__" prefix="label" tag="label"
                        default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Email
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="input" tag="input"
                        type="email"
                        default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <div>
                    <x-dl-wrapper slug="__SLUG__" prefix="label" tag="label"
                        default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Message
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="textarea" tag="textarea"
                        rows="5"
                        default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none">
                    </x-dl-wrapper>
                </div>
                <x-dl-button slug="__SLUG__" prefix="submit" type="submit" default="Send Message"
                    default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
            </x-dl-wrapper>
            <x-dl-wrapper slug="__SLUG__" prefix="sidebar"
                default-classes="space-y-8">
                <div>
                    <x-dl-wrapper slug="__SLUG__" prefix="sidebar_heading" tag="h3"
                        default-classes="text-lg font-semibold text-zinc-900 dark:text-white">
                        Contact Information
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="contact_details"
                        default-classes="mt-4 space-y-4 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p>📍 <x-dl-subheadline slug="__SLUG__" prefix="address_street" tag="span" default="123 Main Street, Suite 100"
                            default-classes="" /><br><x-dl-subheadline slug="__SLUG__" prefix="address_city" tag="span" default="San Francisco, CA 94105"
                            default-classes="" /></p>
                        <p>📞 <x-dl-subheadline slug="__SLUG__" prefix="phone" tag="span" default="(555) 123-4567"
                            default-classes="" /></p>
                        <p>✉️ <x-dl-subheadline slug="__SLUG__" prefix="email" tag="span" default="hello@example.com"
                            default-classes="" /></p>
                    </x-dl-wrapper>
                </div>
                <div>
                    <x-dl-wrapper slug="__SLUG__" prefix="sidebar_heading" tag="h3"
                        default-classes="text-lg font-semibold text-zinc-900 dark:text-white">
                        Business Hours
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="hours"
                        default-classes="mt-4 space-y-1 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p><x-dl-subheadline slug="__SLUG__" prefix="hours_weekday" tag="span" default="Monday–Friday: 9am–6pm PST"
                            default-classes="" /></p>
                        <p><x-dl-subheadline slug="__SLUG__" prefix="hours_saturday" tag="span" default="Saturday: 10am–4pm PST"
                            default-classes="" /></p>
                        <p><x-dl-subheadline slug="__SLUG__" prefix="hours_sunday" tag="span" default="Sunday: Closed"
                            default-classes="" /></p>
                    </x-dl-wrapper>
                </div>
            </x-dl-wrapper>
        </x-dl-wrapper>
</x-dl-section>

{{--
@name Contact - With Map
@description Contact form with an embedded map iframe placeholder.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Find Us"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Visit our office or reach out online."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="content_grid"
        default-classes="grid md:grid-cols-2 gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="map_wrapper"
            default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800">
            <x-dl.wrapper slug="__SLUG__" prefix="map_embed" tag="iframe"
                src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d100000!2d-122.4194!3d37.7749!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                default-classes="w-full h-full" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="form_wrapper"
            default-classes="space-y-5">
            <x-dl.wrapper slug="__SLUG__" prefix="field_name"
                default-classes="">
                <x-dl.wrapper slug="__SLUG__" prefix="input_name" tag="input"
                    type="text" placeholder="Your name"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_email"
                default-classes="">
                <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                    type="email" placeholder="Email address"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="field_message"
                default-classes="">
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

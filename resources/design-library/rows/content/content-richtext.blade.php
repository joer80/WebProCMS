{{--
@name Rich Text Block
@description Full-width centered rich text content area for long-form content.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Story"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.subheadline slug="__SLUG__" prefix="body" tag="div" default="<p>We started with a simple question: why is software so hard to use? After years of watching teams struggle with disconnected tools, we set out to build something better.</p><p>Our platform was born in 2020 from the frustration of trying to coordinate a distributed team across a dozen different apps. We knew there had to be a better way — and we built it.</p><p>Today, we're proud to serve thousands of teams worldwide, from scrappy two-person startups to global enterprises with thousands of employees. Our mission hasn't changed: make great software that works for everyone.</p>"
        default-classes="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300 leading-relaxed space-y-4" />
</x-dl.section>

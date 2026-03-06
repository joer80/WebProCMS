{{--
@name Blog Detail - Content
@description Main article content area with prose styling.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="article_body" tag="article"
        default-classes="prose prose-zinc dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300 leading-relaxed">
        <x-dl.subheadline slug="__SLUG__" prefix="placeholder_text" tag="div" default="<p>Your article content goes here. This row is a placeholder for the main body of your blog post. The actual post content is rendered by the blog detail page template.</p><p>You can use this row to add supplemental content, pull quotes, or additional sections around the main article body.</p>"
            default-classes="" />
    </x-dl.wrapper>
</x-dl.section>

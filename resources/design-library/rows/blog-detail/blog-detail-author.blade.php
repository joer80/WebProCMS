{{--
@name Blog Detail - Author Bio
@description Author bio box with avatar, name, role, and bio text.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="author_card"
        default-classes="p-8 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-start gap-6">
        <x-dl.media slug="__SLUG__"
            default-wrapper-classes="shrink-0 size-20 rounded-full overflow-hidden bg-zinc-200 dark:bg-zinc-700"
            default-image-classes="w-full h-full object-cover"
            default-image="https://placehold.co/200x200" />
        <div>
            <x-dl.subheadline slug="__SLUG__" prefix="about_label" tag="span" default="Written by"
                default-classes="text-xs font-semibold text-primary uppercase tracking-wider" />
            <x-dl.heading slug="__SLUG__" prefix="author_name" default="Author Name"
                default-tag="h3"
                default-classes="mt-1 font-heading text-xl font-bold text-zinc-900 dark:text-white" />
            <x-dl.subheadline slug="__SLUG__" prefix="author_role" default="Content Strategist & Writer"
                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5" />
            <x-dl.subheadline slug="__SLUG__" prefix="author_bio" tag="p" default="Award-winning writer with a passion for technology, design, and helping businesses communicate more clearly."
                default-classes="mt-3 text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed" />
        </div>
    </x-dl.wrapper>
</x-dl.section>

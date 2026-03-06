{{--
@name Blog Detail - Table of Contents
@description Sticky table of contents for long blog posts.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-8 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="toc_card"
        default-classes="p-6 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
        <x-dl.heading slug="__SLUG__" prefix="toc_heading" default="In this article"
            default-tag="h3"
            default-classes="font-heading text-base font-semibold text-zinc-900 dark:text-white mb-4" />
        <x-dl.grid slug="__SLUG__" prefix="toc_items"
            default-grid-classes="space-y-2"
            default-items='[{"label":"Introduction","anchor":"#introduction"},{"label":"Getting Started","anchor":"#getting-started"},{"label":"Key Concepts","anchor":"#key-concepts"},{"label":"Advanced Tips","anchor":"#advanced-tips"},{"label":"Conclusion","anchor":"#conclusion"}]'>
            @dlItems('__SLUG__', 'toc_items', $tocItems, '[{"label":"Introduction","anchor":"#introduction"},{"label":"Getting Started","anchor":"#getting-started"},{"label":"Key Concepts","anchor":"#key-concepts"},{"label":"Advanced Tips","anchor":"#advanced-tips"},{"label":"Conclusion","anchor":"#conclusion"}]')
            @foreach ($tocItems as $i => $item)
                <x-dl.card slug="__SLUG__" prefix="toc_item" tag="a"
                    href="{{ $item['anchor'] }}"
                    default-classes="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors py-1 group">
                    <x-dl.wrapper slug="__SLUG__" prefix="toc_number" tag="span"
                        default-classes="shrink-0 size-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-xs flex items-center justify-center text-zinc-500 group-hover:bg-primary group-hover:text-white transition-colors">
                        {{ $i + 1 }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="toc_label" tag="span"
                        default-classes="">
                        {{ $item['label'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </x-dl.wrapper>
</x-dl.section>

{{--
@name Blog Detail - Social Share
@description Social sharing buttons for the current blog post.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-8 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700"
    default-container-classes="max-w-3xl mx-auto flex flex-wrap items-center gap-4">
    <x-dl.subheadline slug="__SLUG__" prefix="share_label" default="Share this article:"
        default-classes="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mr-2" />
    <x-dl.grid slug="__SLUG__" prefix="share_links"
        default-grid-classes="flex flex-wrap gap-3"
        default-items='[{"label":"Twitter","url":"#","icon":"share"},{"label":"LinkedIn","url":"#","icon":"share"},{"label":"Facebook","url":"#","icon":"share"},{"label":"Copy Link","url":"#","icon":"link"}]'>
        @dlItems('__SLUG__', 'share_links', $shareLinks, '[{"label":"Twitter","url":"#","icon":"share"},{"label":"LinkedIn","url":"#","icon":"share"},{"label":"Facebook","url":"#","icon":"share"},{"label":"Copy Link","url":"#","icon":"link"}]')
        @foreach ($shareLinks as $link)
            <x-dl.card slug="__SLUG__" prefix="share_button" tag="a"
                data-editor-item-index="{{ $loop->index }}"
                href="{{ $link['url'] }}"
                default-classes="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:border-primary hover:text-primary transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="share_icon" name="{{ $link['icon'] }}"
                    default-classes="size-4" />
                <x-dl.wrapper slug="__SLUG__" prefix="share_label_text" tag="span"
                    default-classes="">
                    {{ $link['label'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

{{--
@name Blog Detail - Tags
@description Tag cloud for blog post categorization.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-8 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <div class="flex flex-wrap items-center gap-3">
        <x-dl.wrapper slug="__SLUG__" prefix="tags_label" tag="span"
            default-classes="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mr-1">
            Tags:
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="tags"
            default-grid-classes="flex flex-wrap gap-2"
            default-items='[{"label":"Technology"},{"label":"Design"},{"label":"Business"},{"label":"Tutorial"},{"label":"News"}]'>
            @dlItems('__SLUG__', 'tags', $tags, '[{"label":"Technology"},{"label":"Design"},{"label":"Business"},{"label":"Tutorial"},{"label":"News"}]')
            @foreach ($tags as $tag)
                <x-dl.card slug="__SLUG__" prefix="tag_badge"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="inline-block px-3 py-1 text-sm rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary/10 hover:text-primary transition-colors">
                    {{ $tag['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </div>
</x-dl.section>

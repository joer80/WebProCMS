{{--
@name 404 - With Suggestions
@description 404 page with a grid of helpful page links to guide users.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="max-w-3xl mx-auto text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="error_code"
        default-classes="text-7xl font-black text-zinc-200 dark:text-zinc-700">
        404
    </x-dl.wrapper>
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Page Not Found"
        default-tag="h1"
        default-classes="font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Here are some helpful links to get you back on track."
        default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    <x-dl.grid slug="__SLUG__" prefix="suggestions"
        default-grid-classes="mt-10 grid sm:grid-cols-3 gap-4 text-left"
        default-items='[{"title":"Home","desc":"Back to the main page","url":"/","icon":"home"},{"title":"Blog","desc":"Read our latest articles","url":"/blog","icon":"newspaper"},{"title":"Contact","desc":"Get in touch with us","url":"/contact","icon":"envelope"}]'>
        @dlItems('__SLUG__', 'suggestions', $suggestions, '[{"title":"Home","desc":"Back to the main page","url":"/","icon":"home"},{"title":"Blog","desc":"Read our latest articles","url":"/blog","icon":"newspaper"},{"title":"Contact","desc":"Get in touch with us","url":"/contact","icon":"envelope"}]')
        @foreach ($suggestions as $suggestion)
            <x-dl.card slug="__SLUG__" prefix="suggestion_card" tag="a"
                data-editor-item-index="{{ $loop->index }}"
                href="{{ $suggestion['url'] }}"
                default-classes="p-5 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors group">
                <x-dl.icon slug="__SLUG__" prefix="suggestion_icon" name="{{ $suggestion['icon'] }}"
                    default-wrapper-classes="mb-3 text-primary"
                    default-classes="size-6" />
                <x-dl.wrapper slug="__SLUG__" prefix="suggestion_title" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                    {{ $suggestion['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="suggestion_desc" tag="p"
                    default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $suggestion['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

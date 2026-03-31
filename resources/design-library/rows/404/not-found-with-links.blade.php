{{--
@name 404 - With Links
@description Centered 404 with a grid of helpful navigation cards below.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="max-w-4xl mx-auto text-center py-20">
    <x-dl.wrapper slug="__SLUG__" prefix="error_code"
        default-classes="text-8xl font-black text-zinc-200 dark:text-zinc-700">
        404
    </x-dl.wrapper>
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Page Not Found"
        default-tag="h1"
        default-classes="font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Let us help you find what you're looking for."
        default-classes="mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" />
    <x-dl.grid slug="__SLUG__" prefix="links"
        default-grid-classes="mt-12 grid sm:grid-cols-2 md:grid-cols-4 gap-4 text-left"
        default-items='[{"icon":"home","title":"Home","desc":"Back to the homepage"},{"icon":"information-circle","title":"About","desc":"Learn about us"},{"icon":"document-text","title":"Blog","desc":"Read our articles"},{"icon":"envelope","title":"Contact","desc":"Get in touch"}]'>
        @dlItems('__SLUG__', 'links', $links, '[{"icon":"home","title":"Home","desc":"Back to the homepage"},{"icon":"information-circle","title":"About","desc":"Learn about us"},{"icon":"document-text","title":"Blog","desc":"Read our articles"},{"icon":"envelope","title":"Contact","desc":"Get in touch"}]')
        @foreach ($links as $link)
            <x-dl.card slug="__SLUG__" prefix="link_card"
            data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-primary/50 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="link_icon" name="{{ $link['icon'] }}"
                    default-wrapper-classes="mb-3 text-primary"
                    default-classes="size-6" />
                <x-dl.wrapper slug="__SLUG__" prefix="link_title" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white text-sm">
                    {{ $link['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="link_desc" tag="p"
                    default-classes="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $link['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Go Home"
        default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Contact Support"
        default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl.section>

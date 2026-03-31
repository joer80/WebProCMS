{{--
@name Social Proof - Awards & Badges
@description Row of industry awards, certifications, and recognition badges.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Award-Winning Platform"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Recognized by the industry's most respected analysts and publications."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="awards"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-6"
        default-items='[{"title":"Best Product 2024","org":"TechAwards","icon":"trophy"},{"title":"Top Rated Spring","org":"G2","icon":"star"},{"title":"Editor Choice","org":"PCMag","icon":"check-badge"},{"title":"Innovation Award","org":"Gartner","icon":"light-bulb"}]'>
        @dlItems('__SLUG__', 'awards', $awards, '[{"title":"Best Product 2024","org":"TechAwards","icon":"trophy"},{"title":"Top Rated Spring","org":"G2","icon":"star"},{"title":"Editor Choice","org":"PCMag","icon":"check-badge"},{"title":"Innovation Award","org":"Gartner","icon":"light-bulb"}]')
        @foreach ($awards as $award)
            <x-dl.card slug="__SLUG__" prefix="award_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 text-center hover:border-primary/40 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="award_icon" name="{{ $award['icon'] }}"
                    default-wrapper-classes="mx-auto mb-3 text-primary"
                    default-classes="size-8" />
                <x-dl.wrapper slug="__SLUG__" prefix="award_title" tag="h3"
                    default-classes="font-semibold text-sm text-zinc-900 dark:text-white">
                    {{ $award['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="award_org" tag="p"
                    default-classes="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                    {{ $award['org'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

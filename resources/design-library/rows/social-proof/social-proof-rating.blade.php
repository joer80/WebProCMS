{{--
@name Social Proof - Rating Summary
@description Star rating summary with overall score and review platform badges.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="mb-12">
        <x-dl.wrapper slug="__SLUG__" prefix="stars"
            default-classes="flex justify-center text-primary text-3xl gap-1 mb-4">
            ★★★★★
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="overall_score"
            default-classes="text-5xl font-black text-zinc-900 dark:text-white">
            4.9
        </x-dl.wrapper>
        <x-dl.subheadline slug="__SLUG__" prefix="score_label" default="out of 5 — based on 1,500+ reviews"
            default-classes="mt-2 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="platforms"
        default-grid-classes="grid grid-cols-1 sm:grid-cols-3 gap-6"
        default-items='[{"platform":"G2","score":"4.9","reviews":"500+ reviews"},{"platform":"Capterra","score":"4.8","reviews":"300+ reviews"},{"platform":"Trustpilot","score":"4.9","reviews":"700+ reviews"}]'>
        @dlItems('__SLUG__', 'platforms', $platforms, '[{"platform":"G2","score":"4.9","reviews":"500+ reviews"},{"platform":"Capterra","score":"4.8","reviews":"300+ reviews"},{"platform":"Trustpilot","score":"4.9","reviews":"700+ reviews"}]')
        @foreach ($platforms as $platform)
            <x-dl.card slug="__SLUG__" prefix="platform_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700">
                <x-dl.wrapper slug="__SLUG__" prefix="platform_name" tag="h3"
                    default-classes="font-bold text-lg text-zinc-900 dark:text-white">
                    {{ $platform['platform'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="platform_score"
                    default-classes="mt-2 text-3xl font-black text-primary">
                    {{ $platform['score'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="platform_reviews" tag="p"
                    default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $platform['reviews'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

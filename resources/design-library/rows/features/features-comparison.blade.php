{{--
@name Features - Comparison
@description Side-by-side comparison of your advantages versus competitors.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="How We Compare"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="See why teams switch to us and never look back."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="comparison_grid"
        default-classes="grid md:grid-cols-2 gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="us_column"
            default-classes="p-8 rounded-card bg-primary/5 border-2 border-primary">
            <x-dl.wrapper slug="__SLUG__" prefix="us_heading" tag="h3"
                default-classes="text-lg font-bold text-primary mb-6">
                With Us ✓
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="pros"
                default-grid-classes="space-y-3"
                default-items='[{"title":"Unlimited team members"},{"title":"Real-time collaboration"},{"title":"99.9% uptime SLA"},{"title":"24/7 live support"},{"title":"Advanced analytics included"},{"title":"No setup fees ever"}]'>
                @dlItems('__SLUG__', 'pros', $pros, '[{"title":"Unlimited team members"},{"title":"Real-time collaboration"},{"title":"99.9% uptime SLA"},{"title":"24/7 live support"},{"title":"Advanced analytics included"},{"title":"No setup fees ever"}]')
                @foreach ($pros as $pro)
                    <x-dl.card slug="__SLUG__" prefix="pro_item"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center gap-3">
                        <x-dl.icon slug="__SLUG__" prefix="pro_icon" name="check-circle"
                            default-classes="size-5 shrink-0 text-primary" />
                        <x-dl.wrapper slug="__SLUG__" prefix="pro_text" tag="span"
                            default-classes="text-zinc-700 dark:text-zinc-300 font-medium">
                            {{ $pro['title'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="others_column"
            default-classes="p-8 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
            <x-dl.wrapper slug="__SLUG__" prefix="others_heading" tag="h3"
                default-classes="text-lg font-bold text-zinc-400 mb-6">
                The Competition ✗
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="cons"
                default-grid-classes="space-y-3"
                default-items='[{"title":"Per-seat pricing adds up"},{"title":"Async-only collaboration"},{"title":"No SLA on lower plans"},{"title":"Email support only"},{"title":"Analytics as paid add-on"},{"title":"Expensive onboarding fees"}]'>
                @dlItems('__SLUG__', 'cons', $cons, '[{"title":"Per-seat pricing adds up"},{"title":"Async-only collaboration"},{"title":"No SLA on lower plans"},{"title":"Email support only"},{"title":"Analytics as paid add-on"},{"title":"Expensive onboarding fees"}]')
                @foreach ($cons as $con)
                    <x-dl.card slug="__SLUG__" prefix="con_item"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center gap-3">
                        <x-dl.icon slug="__SLUG__" prefix="con_icon" name="x-circle"
                            default-classes="size-5 shrink-0 text-zinc-400" />
                        <x-dl.wrapper slug="__SLUG__" prefix="con_text" tag="span"
                            default-classes="text-zinc-400 line-through">
                            {{ $con['title'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

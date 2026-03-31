{{--
@name Slider - Card Carousel
@description Horizontally scrollable card carousel with arrow navigation.
@sort 30
--}}
@dlItems('__SLUG__', 'cards', $cards, '[{"icon":"bolt","title":"Card One","desc":"Description for the first card."},{"icon":"shield-check","title":"Card Two","desc":"Description for the second card."},{"icon":"chart-bar","title":"Card Three","desc":"Description for the third card."},{"icon":"globe-alt","title":"Card Four","desc":"Description for the fourth card."},{"icon":"heart","title":"Card Five","desc":"Description for the fifth card."}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 overflow-hidden"
    default-container-classes="max-w-6xl mx-auto"
    x-data="{
        scrollEl: null,
        init() { this.scrollEl = this.$el.querySelector('[data-scroll]'); },
        prev() { this.scrollEl.scrollBy({ left: -320, behavior: 'smooth' }); },
        next() { this.scrollEl.scrollBy({ left: 320, behavior: 'smooth' }); }
    }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-8">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Featured Cards"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.wrapper slug="__SLUG__" prefix="nav_buttons"
            default-classes="flex gap-2">
            <button @click="prev()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button @click="next()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="scroll_container"
        default-classes="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide"
        data-scroll="">
        @foreach ($cards as $card)
            <x-dl.card slug="__SLUG__" prefix="slide_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex-none w-72 p-6 rounded-card border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 snap-start">
                <x-dl.icon slug="__SLUG__" prefix="card_icon" name="{{ $card['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-7" />
                <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $card['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    {{ $card['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>

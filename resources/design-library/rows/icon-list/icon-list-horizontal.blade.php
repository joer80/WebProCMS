{{--
@name Icon List - Horizontal
@description Horizontal row of icon + label pairs, great for trust signals or features.
@sort 10
--}}
<section class="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="flex items-center gap-3">
                <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">✓</div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ content('__SLUG__', 'item_1', 'No credit card required', 'text', 'trust signals') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">✓</div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ content('__SLUG__', 'item_2', '14-day free trial', 'text', 'trust signals') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">✓</div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ content('__SLUG__', 'item_3', 'Cancel anytime', 'text', 'trust signals') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">✓</div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ content('__SLUG__', 'item_4', 'SOC 2 compliant', 'text', 'trust signals') }}</span>
            </div>
        </div>
    </div>
</section>

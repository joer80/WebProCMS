{{--
@name Icon List - Horizontal
@description Horizontal row of icon + label pairs, great for trust signals or features.
@sort 10
--}}
<section class="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            @foreach ([['icon' => '✓', 'label' => 'No credit card required'], ['icon' => '✓', 'label' => '14-day free trial'], ['icon' => '✓', 'label' => 'Cancel anytime'], ['icon' => '✓', 'label' => 'SOC 2 compliant']] as $item)
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                        {{ $item['icon'] }}
                    </div>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

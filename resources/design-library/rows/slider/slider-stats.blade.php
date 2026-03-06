{{--
@name Slider - Stats Counter
@description Animated statistics counter with count-up effect on viewport entry.
@sort 90
--}}
@dlItems('__SLUG__', 'stats', $stats, '[{"number":"10000","suffix":"+","label":"Customers"},{"number":"99","suffix":"%","label":"Uptime"},{"number":"50","suffix":"+","label":"Countries"},{"number":"5","suffix":"/5","label":"Rating"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-primary"
    default-container-classes="max-w-6xl mx-auto"
    x-data="{
        animated: false,
        counts: {},
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.animated) {
                        this.animated = true;
                        @foreach ($stats as $i => $stat)
                        this.animateCount({{ $i }}, 0, {{ (int) $stat['number'] }}, 2000);
                        @endforeach
                    }
                });
            }, { threshold: 0.3 });
            observer.observe(this.$el);
        },
        animateCount(idx, start, end, duration) {
            const startTime = performance.now();
            const update = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                this.counts[idx] = Math.floor(progress * (end - start) + start);
                if (progress < 1) { requestAnimationFrame(update); }
                else { this.counts[idx] = end; }
            };
            requestAnimationFrame(update);
        }
    }">
    <x-dl.grid slug="__SLUG__" prefix="stats"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach ($stats as $i => $stat)
            <x-dl.card slug="__SLUG__" prefix="stat_item"
                default-classes="p-6">
                <x-dl.wrapper slug="__SLUG__" prefix="stat_display"
                    default-classes="text-5xl font-black text-white">
                    <span x-text="(counts[{{ $i }}] ?? 0).toLocaleString() + '{{ $stat['suffix'] }}'">{{ number_format((int) $stat['number']) }}{{ $stat['suffix'] }}</span>
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="stat_label" tag="p"
                    default-classes="mt-2 text-white/70 font-medium">
                    {{ $stat['label'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

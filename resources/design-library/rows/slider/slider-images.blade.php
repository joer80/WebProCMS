{{--
@name Slider - Image Slider
@description Simple image carousel with navigation arrows and dot indicators.
@sort 50
--}}
@dlItems('__SLUG__', 'slides', $slides, '[{"image":"https://placehold.co/1200x675","alt":"Slide 1","caption":""},{"image":"https://placehold.co/1200x675","alt":"Slide 2","caption":""},{"image":"https://placehold.co/1200x675","alt":"Slide 3","caption":""}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-900 overflow-hidden"
    default-container-classes="max-w-5xl mx-auto"
    x-data="{
        current: 0,
        total: {{ count($slides) }},
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
        }
    }"
    x-init="autoPlay()">
    <x-dl.wrapper slug="__SLUG__" prefix="slider_wrapper"
        default-classes="relative rounded-card overflow-hidden">
        @foreach ($slides as $i => $slide)
            @php $slideUrl = $slide['image'] ? (str_starts_with($slide['image'], 'http') ? $slide['image'] : \Illuminate\Support\Facades\Storage::url($slide['image'])) : null; @endphp
            <div x-show="current === {{ $i }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">
                <x-dl.wrapper slug="__SLUG__" prefix="slide_image_wrapper"
                    default-classes="aspect-video bg-zinc-100 dark:bg-zinc-800">
                    @if ($slideUrl)
                        <img src="{{ $slideUrl }}" alt="{{ $slide['alt'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400">{{ $slide['alt'] }}</div>
                    @endif
                </x-dl.wrapper>
                @if (!empty($slide['caption']))
                    <x-dl.wrapper slug="__SLUG__" prefix="slide_caption" tag="p"
                        default-classes="mt-3 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $slide['caption'] }}
                    </x-dl.wrapper>
                @endif
            </div>
        @endforeach
        {{-- Prev/Next buttons --}}
        <button @click="current = (current - 1 + total) % total" class="absolute left-3 top-1/2 -translate-y-1/2 size-9 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </button>
        <button @click="current = (current + 1) % total" class="absolute right-3 top-1/2 -translate-y-1/2 size-9 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="dots_wrapper"
        default-classes="flex items-center justify-center gap-2 mt-4">
        @foreach ($slides as $i => $slide)
            <button @click="current = {{ $i }}" :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>

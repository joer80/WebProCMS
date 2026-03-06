{{--
@name Slider - Hero
@description Full-width hero image slider with headline and CTA on each slide.
@sort 20
--}}
@dlItems('__SLUG__', 'slides', $slides, '[{"headline":"Build Something Amazing","subheadline":"Start your journey today with our powerful platform.","cta":"Get Started","cta_url":"#","image":"https://placehold.co/1600x900"},{"headline":"Scale Without Limits","subheadline":"Handle any load with our enterprise-grade infrastructure.","cta":"Learn More","cta_url":"#","image":"https://placehold.co/1600x900"},{"headline":"Trusted by Thousands","subheadline":"Join the teams that rely on us every single day.","cta":"See Reviews","cta_url":"#","image":"https://placehold.co/1600x900"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="relative overflow-hidden bg-zinc-900"
    default-container-classes=""
    x-data="{
        current: 0,
        total: {{ count($slides) }},
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 5000);
        }
    }"
    x-init="autoPlay()">
    @foreach ($slides as $i => $slide)
        @php $slideUrl = $slide['image'] ? (str_starts_with($slide['image'], 'http') ? $slide['image'] : \Illuminate\Support\Facades\Storage::url($slide['image'])) : null; @endphp
        <div x-show="current === {{ $i }}"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="relative">
            <x-dl.wrapper slug="__SLUG__" prefix="slide_bg_wrapper"
                default-classes="aspect-[21/9] bg-zinc-800 overflow-hidden">
                @if ($slideUrl)
                    <img src="{{ $slideUrl }}" alt="{{ $slide['headline'] }}" class="w-full h-full object-cover opacity-60">
                @endif
                <x-dl.wrapper slug="__SLUG__" prefix="slide_overlay"
                    default-classes="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="slide_content"
                default-classes="absolute inset-0 flex items-center justify-center text-center px-6">
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="slide_headline" tag="h2"
                        default-classes="font-heading text-4xl sm:text-6xl font-bold text-white">
                        {{ $slide['headline'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="slide_subheadline" tag="p"
                        default-classes="mt-4 text-xl text-white/80">
                        {{ $slide['subheadline'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="slide_cta" tag="a"
                        href="{{ $slide['cta_url'] }}"
                        default-classes="mt-8 inline-block px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                        {{ $slide['cta'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.wrapper>
        </div>
    @endforeach
    <button @click="current = (current - 1 + total) % total" class="absolute left-4 top-1/2 -translate-y-1/2 size-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors z-10">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </button>
    <button @click="current = (current + 1) % total" class="absolute right-4 top-1/2 -translate-y-1/2 size-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors z-10">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </button>
    <x-dl.wrapper slug="__SLUG__" prefix="dots_wrapper"
        default-classes="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-2">
        @foreach ($slides as $i => $slide)
            <button @click="current = {{ $i }}" :class="current === {{ $i }} ? 'bg-white w-6' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>

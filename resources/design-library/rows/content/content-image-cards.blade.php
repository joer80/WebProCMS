{{--
@name Cards Grid with Images
@description Centered heading and subheadline above a grid of image cards, each with a title and description.
@sort 25
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Complete Care When You Need It Most"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Whether you're treating an urgent issue, need diagnostic testing, or focusing on long-term wellness, we provide comprehensive services all under one roof."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400 text-center max-w-3xl mx-auto mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="cards"
        default-grid-classes="grid sm:grid-cols-2 md:grid-cols-3 gap-8"
        default-items='[{"image":"","image_alt":"Illness & Injury","title":"Illness & Injury","desc":"Walk in for fast treatment of common illnesses and injuries. Most major insurance plans welcome, plus affordable self-pay options."},{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit."},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management and hormone optimization."}]'>
        @dlItems('__SLUG__', 'cards', $cards, '[{"image":"","image_alt":"Illness & Injury","title":"Illness & Injury","desc":"Walk in for fast treatment of common illnesses and injuries. Most major insurance plans welcome, plus affordable self-pay options."},{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit."},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management and hormone optimization."}]')
        @foreach ($cards as $card)
            @php $cardImg = !empty($card['image']) ? (str_starts_with($card['image'], 'http') ? $card['image'] : \Illuminate\Support\Facades\Storage::url($card['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="card"
                default-classes="rounded-card overflow-hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-card">
                <x-dl.wrapper slug="__SLUG__" prefix="card_image_wrapper"
                    default-classes="aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                    @if ($cardImg)
                        <img src="{{ $cardImg }}" alt="{{ $card['image_alt'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_body" default-classes="p-6">
                    <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                        default-classes="font-heading text-xl font-bold text-zinc-900 dark:text-white mb-2">
                        {{ $card['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400 text-base leading-relaxed">
                        {{ $card['desc'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>

</x-dl.section>

{{--
@name Featured Card Grid 2
@description Centered heading above a large featured card (image on top, text below) on the left with three smaller image cards stacked on the right.
@sort 27
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-[3fr_2fr] gap-6 items-stretch">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Complete Care When You Need It Most"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center md:col-span-2" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Whether you're treating an urgent issue, need diagnostic testing, or focusing on long-term wellness, we provide comprehensive services all under one roof."
        default-classes="mt-4 mb-4 text-lg text-zinc-500 dark:text-zinc-400 text-center max-w-3xl mx-auto md:col-span-2" />

    {{-- Left: card with image on top, text content below --}}
    <x-dl.wrapper slug="__SLUG__" prefix="featured_card"
        default-classes="rounded-card overflow-hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm flex flex-col">
        @php $hasFeaturedImg = (bool) content('__SLUG__', 'featured_image_image', ''); @endphp
        <x-dl.image slug="__SLUG__" prefix="featured_image"
            default-wrapper-classes="w-full aspect-3/1 overflow-hidden bg-zinc-100 dark:bg-zinc-700"
            default-image-classes="w-full h-full object-cover" />
        @if (!$hasFeaturedImg)
            <div class="w-full aspect-3/1 bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        @endif
        <x-dl.wrapper slug="__SLUG__" prefix="featured_content"
            default-classes="flex flex-col flex-1 p-8 gap-4">
            <x-dl.heading slug="__SLUG__" prefix="featured_title" default="Illness & Injury"
                default-tag="h3"
                default-classes="font-heading text-2xl font-bold text-zinc-900 dark:text-white" />
            <x-dl.subheadline slug="__SLUG__" prefix="featured_desc" default="Walk in for fast treatment of common illnesses and injuries. Most major insurance plans welcome, plus affordable self-pay options."
                default-classes="text-zinc-500 dark:text-zinc-400 text-base leading-relaxed" />
            <x-dl.link slug="__SLUG__" prefix="featured_cta"
                default-label="Learn More →"
                default-url="#"
                default-classes="mt-auto inline-flex items-center text-primary font-semibold text-sm hover:text-primary/80 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>

    {{-- Right: three smaller cards stacked vertically --}}
    <x-dl.grid slug="__SLUG__" prefix="cards"
        default-grid-classes="grid gap-4"
        default-object-fit="cover"
        default-items='[{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit.","link":"#","link_label":"Learn More →"},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management.","link":"#","link_label":"Learn More →"},{"image":"","image_alt":"Occupational Medicine","title":"Occupational Medicine","desc":"Comprehensive occupational health services tailored for your workforce.","link":"#","link_label":"Learn More →"}]'>
        @dlItems('__SLUG__', 'cards', $cards, '[{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit.","link":"#","link_label":"Learn More →"},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management.","link":"#","link_label":"Learn More →"},{"image":"","image_alt":"Occupational Medicine","title":"Occupational Medicine","desc":"Comprehensive occupational health services tailored for your workforce.","link":"#","link_label":"Learn More →"}]')
        @foreach ($cards as $card)
            @php $cardImg = !empty($card['image']) ? (str_starts_with($card['image'], 'http') ? $card['image'] : \Illuminate\Support\Facades\Storage::url($card['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="rounded-card overflow-hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm flex">
                <x-dl.wrapper slug="__SLUG__" prefix="card_body" default-classes="flex-1 p-4 flex flex-col min-w-0">
                    <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                        default-classes="font-heading text-base font-bold text-zinc-900 dark:text-white mb-1">
                        {{ $card['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $card['desc'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_link" tag="a" :href="($card['link'] ?? '') ?: '#'"
                        default-classes="mt-auto pt-2 inline-flex items-center text-primary font-semibold text-sm hover:text-primary/80 transition-colors">
                        {{ $card['link_label'] ?? 'Learn More →' }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_image_wrapper"
                    default-classes="w-36 shrink-0 overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                    @if ($cardImg)
                        <img src="{{ $cardImg }}" alt="{{ $card['image_alt'] }}" class="w-full h-full {{ match(content('__SLUG__', 'cards_object_fit', 'cover')) { 'contain' => 'object-contain', 'fill' => 'object-fill', 'none' => 'object-none', default => 'object-cover' } }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    @endif
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

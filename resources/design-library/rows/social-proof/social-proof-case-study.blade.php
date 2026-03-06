{{--
@name Social Proof - Case Study
@description Featured case study card with company results, quote, and metrics.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="eyebrow" tag="span" default="Customer Story"
            default-classes="text-sm font-semibold text-primary uppercase tracking-wider" />
        <x-dl.heading slug="__SLUG__" prefix="company" default="How Acme Corp Grew 3× in 12 Months"
            default-tag="h2"
            default-classes="font-heading mt-3 text-3xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="quote" tag="blockquote" default="Switching to this platform was the single best decision we made last year. Our team velocity doubled and we couldn't be happier."
            default-classes="mt-6 text-zinc-600 dark:text-zinc-300 italic border-l-4 border-primary pl-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="attribution" default="— Jane Smith, COO at Acme Corp"
            default-classes="mt-3 text-sm font-medium text-zinc-500 dark:text-zinc-400" />
        <x-dl.grid slug="__SLUG__" prefix="results"
            default-grid-classes="mt-8 grid grid-cols-3 gap-6"
            default-items='[{"number":"3×","label":"Revenue Growth"},{"number":"50%","label":"Faster Delivery"},{"number":"90%","label":"Team Satisfaction"}]'>
            @dlItems('__SLUG__', 'results', $results, '[{"number":"3×","label":"Revenue Growth"},{"number":"50%","label":"Faster Delivery"},{"number":"90%","label":"Team Satisfaction"}]')
            @foreach ($results as $result)
                <x-dl.card slug="__SLUG__" prefix="result_item"
                    default-classes="">
                    <x-dl.wrapper slug="__SLUG__" prefix="result_number"
                        default-classes="text-3xl font-black text-primary">
                        {{ $result['number'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="result_label" tag="p"
                        default-classes="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ $result['label'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.link slug="__SLUG__" prefix="read_more"
            default-label="Read the full case study →"
            default-url="#"
            default-classes="mt-8 inline-block text-primary font-semibold hover:text-primary/80 transition-colors" />
    </div>
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800"
        default-image-classes="w-full h-full object-cover"
        default-image="https://placehold.co/800x600" />
</x-dl.section>

{{--
@name Text & CTA with Image Cards
@description Two-column section with heading and CTA on the left, and a stacked list of cards with thumbnail images on the right.
@sort 15
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Complete Care When You Need It Most"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="body" tag="p" default="Whether you're treating an urgent issue, need diagnostic testing, or focusing on long-term wellness, we provide comprehensive services all under one roof."
            default-classes="mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8"
            default-primary-label="Our Locations"
            default-primary-classes="btn-primary"
            default-secondary-label=""
            default-secondary-classes="" />
    </div>
    <x-dl.grid slug="__SLUG__" prefix="services"
        default-grid-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"title":"Illness & Injury","desc":"Visit us for fast treatment. Most major insurance plans welcome, plus affordable self-pay options.","image":"https://placehold.co/160x120"},{"title":"X-Rays & Lab Work","desc":"On-site diagnostics so you get answers and treatment in one visit.","image":"https://placehold.co/160x120"},{"title":"Wellness & Preventive Care","desc":"Proactive health services to help you look better, feel better, and live healthier—from hormone management to preventive screenings.","image":"https://placehold.co/160x120"}]'>
        @dlItems('__SLUG__', 'services', $services, '[{"title":"Illness & Injury","desc":"Visit us for fast treatment. Most major insurance plans welcome, plus affordable self-pay options.","image":"https://placehold.co/160x120"},{"title":"X-Rays & Lab Work","desc":"On-site diagnostics so you get answers and treatment in one visit.","image":"https://placehold.co/160x120"},{"title":"Wellness & Preventive Care","desc":"Proactive health services to help you look better, feel better, and live healthier—from hormone management to preventive screenings.","image":"https://placehold.co/160x120"}]')
        @foreach ($services as $service)
            @php $serviceImg = $service['image'] ? (str_starts_with($service['image'], 'http') ? $service['image'] : \Illuminate\Support\Facades\Storage::url($service['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="service_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-center gap-4 py-5">
                <x-dl.wrapper slug="__SLUG__" prefix="service_content"
                    default-classes="flex-1">
                    <x-dl.wrapper slug="__SLUG__" prefix="service_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white text-base">
                        {{ $service['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="service_desc" tag="p"
                        default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $service['desc'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="service_image_wrapper"
                    default-classes="shrink-0 w-28 h-20 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    @if ($serviceImg)
                        <img src="{{ $serviceImg }}" alt="{{ $service['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-zinc-200 dark:bg-zinc-700"></div>
                    @endif
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

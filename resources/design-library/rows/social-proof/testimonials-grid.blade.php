{{--
@name Social Proof - Testimonials Grid
@description Three-column testimonial cards with quotes and author details.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        @php $cardClasses = content('__SLUG__', 'card_classes', 'p-6 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700'); @endphp
        @php $starsClasses = content('__SLUG__', 'stars_classes', 'flex text-primary mb-4'); @endphp
        @php $quoteClasses = content('__SLUG__', 'quote_classes', 'text-zinc-700 dark:text-zinc-300 leading-relaxed italic'); @endphp
        @php $authorRowClasses = content('__SLUG__', 'author_row_classes', 'mt-6 flex items-center gap-3'); @endphp
        @php $avatarClasses = content('__SLUG__', 'avatar_classes', 'size-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center text-sm font-semibold text-zinc-600 dark:text-zinc-300'); @endphp
        @php $authorNameClasses = content('__SLUG__', 'author_name_classes', 'text-sm font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $authorRoleClasses = content('__SLUG__', 'author_role_classes', 'text-xs text-zinc-500 dark:text-zinc-400'); @endphp
        <x-dl-content-header slug="__SLUG__"
            default-wrapper-classes="text-center mb-16"
            default-heading="Loved by Thousands"
            default-heading-tag="h2"
            default-heading-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white"
            default-subheadline="Here's what our customers have to say."
            default-subheadline-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl-grid slug="__SLUG__" prefix="testimonials"
            default-grid-classes="grid md:grid-cols-3 gap-6"
            default-items='[{"quote":"This product completely transformed how our team works. I can\u0027t imagine going back.","name":"Sarah Johnson","role":"CEO at Acme Co"},{"quote":"The best investment we\u0027ve made this year. Setup was a breeze and support is incredible.","name":"Mark Davis","role":"CTO at BuildIt"},{"quote":"Our productivity has doubled since we started using this. Highly recommended.","name":"Lisa Chen","role":"Product Manager at TechCorp"}]'>
            @php $testimonials = json_decode(content('__SLUG__', 'grid_testimonials', ''), true) ?: []; @endphp
            @foreach ($testimonials as $testimonial)
                <div class="{{ $cardClasses }}">
                    <div class="{{ $starsClasses }}">
                        @for ($i = 0; $i < 5; $i++) ★ @endfor
                    </div>
                    <p class="{{ $quoteClasses }}">"{{ $testimonial['quote'] }}"</p>
                    <div class="{{ $authorRowClasses }}">
                        <div class="{{ $avatarClasses }}">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="{{ $authorNameClasses }}">{{ $testimonial['name'] }}</div>
                            <div class="{{ $authorRoleClasses }}">{{ $testimonial['role'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </x-dl-grid>
</x-dl-section>

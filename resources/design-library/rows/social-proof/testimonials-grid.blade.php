{{--
@name Social Proof - Testimonials Grid
@description Three-column testimonial cards with quotes and author details.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16', 'classes', 'content'); @endphp
        @php $testimonialsGridClasses = content('__SLUG__', 'testimonials_grid_classes', 'grid md:grid-cols-3 gap-6', 'classes', 'content'); @endphp
        @php $cardClasses = content('__SLUG__', 'card_classes', 'p-6 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700', 'classes', 'content'); @endphp
        @php $starsClasses = content('__SLUG__', 'stars_classes', 'flex text-primary mb-4', 'classes', 'content'); @endphp
        @php $quoteClasses = content('__SLUG__', 'quote_classes', 'text-zinc-700 dark:text-zinc-300 leading-relaxed italic', 'classes', 'content'); @endphp
        @php $authorRowClasses = content('__SLUG__', 'author_row_classes', 'mt-6 flex items-center gap-3', 'classes', 'content'); @endphp
        @php $avatarClasses = content('__SLUG__', 'avatar_classes', 'size-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center text-sm font-semibold text-zinc-600 dark:text-zinc-300', 'classes', 'content'); @endphp
        @php $authorNameClasses = content('__SLUG__', 'author_name_classes', 'text-sm font-semibold text-zinc-900 dark:text-white', 'classes', 'content'); @endphp
        @php $authorRoleClasses = content('__SLUG__', 'author_role_classes', 'text-xs text-zinc-500 dark:text-zinc-400', 'classes', 'content'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Loved by Thousands', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Here\'s what our customers have to say.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        <div class="{{ $testimonialsGridClasses }}">
            @foreach ([['quote' => 'This product completely transformed how our team works. I can\'t imagine going back.', 'name' => 'Sarah Johnson', 'role' => 'CEO at Acme Co'], ['quote' => 'The best investment we\'ve made this year. Setup was a breeze and support is incredible.', 'name' => 'Mark Davis', 'role' => 'CTO at BuildIt'], ['quote' => 'Our productivity has doubled since we started using this. Highly recommended.', 'name' => 'Lisa Chen', 'role' => 'Product Manager at TechCorp']] as $testimonial)
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
        </div>
    </div>
</section>

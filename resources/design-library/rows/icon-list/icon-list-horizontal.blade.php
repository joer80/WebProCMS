{{--
@name Icon List - Horizontal
@description Horizontal row of icon + label pairs, great for trust signals or features.
@sort 10
--}}
{{-- TODO: review for x-dl-* component adoption --}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-5xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $gridClasses = content('__SLUG__', 'grid_classes', 'grid grid-cols-2 md:grid-cols-4 gap-8'); @endphp
        @php $itemClasses = content('__SLUG__', 'item_classes', 'flex items-center gap-3'); @endphp
        @php $iconClasses = content('__SLUG__', 'icon_classes', 'size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0'); @endphp
        @php $labelClasses = content('__SLUG__', 'label_classes', 'text-sm font-medium text-zinc-700 dark:text-zinc-300'); @endphp
        <div class="{{ $gridClasses }}">
            <div class="{{ $itemClasses }}">
                <div class="{{ $iconClasses }}">✓</div>
                <span class="{{ $labelClasses }}">{{ content('__SLUG__', 'item_1', 'No credit card required') }}</span>
            </div>
            <div class="{{ $itemClasses }}">
                <div class="{{ $iconClasses }}">✓</div>
                <span class="{{ $labelClasses }}">{{ content('__SLUG__', 'item_2', '14-day free trial') }}</span>
            </div>
            <div class="{{ $itemClasses }}">
                <div class="{{ $iconClasses }}">✓</div>
                <span class="{{ $labelClasses }}">{{ content('__SLUG__', 'item_3', 'Cancel anytime') }}</span>
            </div>
            <div class="{{ $itemClasses }}">
                <div class="{{ $iconClasses }}">✓</div>
                <span class="{{ $labelClasses }}">{{ content('__SLUG__', 'item_4', 'SOC 2 compliant') }}</span>
            </div>
        </div>
    </div>
</section>

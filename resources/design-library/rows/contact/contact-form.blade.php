{{--
@name Contact - Form
@description Contact section with form fields and contact details sidebar.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-5xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16'); @endphp
        @php $layoutClasses = content('__SLUG__', 'layout_classes', 'grid md:grid-cols-2 gap-12'); @endphp
        @php $formClasses = content('__SLUG__', 'form_classes', 'space-y-6'); @endphp
        @php $nameGridClasses = content('__SLUG__', 'name_grid_classes', 'grid sm:grid-cols-2 gap-4'); @endphp
        @php $labelClasses = content('__SLUG__', 'label_classes', 'block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1'); @endphp
        @php $inputClasses = content('__SLUG__', 'input_classes', 'w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition'); @endphp
        @php $textareaClasses = content('__SLUG__', 'textarea_classes', 'w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none'); @endphp
        @php $sidebarClasses = content('__SLUG__', 'sidebar_classes', 'space-y-8'); @endphp
        @php $sidebarHeadingClasses = content('__SLUG__', 'sidebar_heading_classes', 'text-lg font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $contactDetailsClasses = content('__SLUG__', 'contact_details_classes', 'mt-4 space-y-4 text-zinc-500 dark:text-zinc-400 text-sm'); @endphp
        @php $hoursClasses = content('__SLUG__', 'hours_classes', 'mt-4 space-y-1 text-zinc-500 dark:text-zinc-400 text-sm'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Get in Touch'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        <div class="{{ $layoutClasses }}">
            <form class="{{ $formClasses }}">
                <div class="{{ $nameGridClasses }}">
                    <div>
                        <label class="{{ $labelClasses }}">First Name</label>
                        <input type="text" class="{{ $inputClasses }}" />
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Last Name</label>
                        <input type="text" class="{{ $inputClasses }}" />
                    </div>
                </div>
                <div>
                    <label class="{{ $labelClasses }}">Email</label>
                    <input type="email" class="{{ $inputClasses }}" />
                </div>
                <div>
                    <label class="{{ $labelClasses }}">Message</label>
                    <textarea rows="5" class="{{ $textareaClasses }}"></textarea>
                </div>
                @php $buttonClasses = content('__SLUG__', 'button_classes', 'w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
                <button type="submit" class="{{ $buttonClasses }}">
                    {{ content('__SLUG__', 'button_label', 'Send Message') }}
                </button>
            </form>
            <div class="{{ $sidebarClasses }}">
                <div>
                    <h3 class="{{ $sidebarHeadingClasses }}">Contact Information</h3>
                    <div class="{{ $contactDetailsClasses }}">
                        <p>📍 {{ content('__SLUG__', 'address_street', '123 Main Street, Suite 100') }}<br>{{ content('__SLUG__', 'address_city', 'San Francisco, CA 94105') }}</p>
                        <p>📞 {{ content('__SLUG__', 'phone', '(555) 123-4567') }}</p>
                        <p>✉️ {{ content('__SLUG__', 'email', 'hello@example.com') }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="{{ $sidebarHeadingClasses }}">Business Hours</h3>
                    <div class="{{ $hoursClasses }}">
                        <p>{{ content('__SLUG__', 'hours_weekday', 'Monday–Friday: 9am–6pm PST') }}</p>
                        <p>{{ content('__SLUG__', 'hours_saturday', 'Saturday: 10am–4pm PST') }}</p>
                        <p>{{ content('__SLUG__', 'hours_sunday', 'Sunday: Closed') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{--
@name Contact - Form
@description Contact section with form fields and contact details sidebar.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-5xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16', 'classes', 'content'); @endphp
        @php $layoutClasses = content('__SLUG__', 'layout_classes', 'grid md:grid-cols-2 gap-12', 'classes', 'content'); @endphp
        @php $formClasses = content('__SLUG__', 'form_classes', 'space-y-6', 'classes', 'content'); @endphp
        @php $nameGridClasses = content('__SLUG__', 'name_grid_classes', 'grid sm:grid-cols-2 gap-4', 'classes', 'content'); @endphp
        @php $labelClasses = content('__SLUG__', 'label_classes', 'block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1', 'classes', 'content'); @endphp
        @php $inputClasses = content('__SLUG__', 'input_classes', 'w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition', 'classes', 'content'); @endphp
        @php $textareaClasses = content('__SLUG__', 'textarea_classes', 'w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none', 'classes', 'content'); @endphp
        @php $sidebarClasses = content('__SLUG__', 'sidebar_classes', 'space-y-8', 'classes', 'content'); @endphp
        @php $sidebarHeadingClasses = content('__SLUG__', 'sidebar_heading_classes', 'text-lg font-semibold text-zinc-900 dark:text-white', 'classes', 'content'); @endphp
        @php $contactDetailsClasses = content('__SLUG__', 'contact_details_classes', 'mt-4 space-y-4 text-zinc-500 dark:text-zinc-400 text-sm', 'classes', 'content'); @endphp
        @php $hoursClasses = content('__SLUG__', 'hours_classes', 'mt-4 space-y-1 text-zinc-500 dark:text-zinc-400 text-sm', 'classes', 'content'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Get in Touch', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
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
                @php $buttonClasses = content('__SLUG__', 'button_classes', 'w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'content'); @endphp
                <button type="submit" class="{{ $buttonClasses }}">
                    {{ content('__SLUG__', 'button_label', 'Send Message', 'text', 'content') }}
                </button>
            </form>
            <div class="{{ $sidebarClasses }}">
                <div>
                    <h3 class="{{ $sidebarHeadingClasses }}">Contact Information</h3>
                    <div class="{{ $contactDetailsClasses }}">
                        <p>📍 {{ content('__SLUG__', 'address_street', '123 Main Street, Suite 100', 'text', 'contact details') }}<br>{{ content('__SLUG__', 'address_city', 'San Francisco, CA 94105', 'text', 'contact details') }}</p>
                        <p>📞 {{ content('__SLUG__', 'phone', '(555) 123-4567', 'text', 'contact details') }}</p>
                        <p>✉️ {{ content('__SLUG__', 'email', 'hello@example.com', 'text', 'contact details') }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="{{ $sidebarHeadingClasses }}">Business Hours</h3>
                    <div class="{{ $hoursClasses }}">
                        <p>{{ content('__SLUG__', 'hours_weekday', 'Monday–Friday: 9am–6pm PST', 'text', 'contact details') }}</p>
                        <p>{{ content('__SLUG__', 'hours_saturday', 'Saturday: 10am–4pm PST', 'text', 'contact details') }}</p>
                        <p>{{ content('__SLUG__', 'hours_sunday', 'Sunday: Closed', 'text', 'contact details') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

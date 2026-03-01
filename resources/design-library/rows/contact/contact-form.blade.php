{{--
@name Contact - Form
@description Contact section with form fields and contact details sidebar.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-5xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div class="text-center mb-16">
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
        <div class="grid md:grid-cols-2 gap-12">
            <form class="space-y-6">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">First Name</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Last Name</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                    <input type="email" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Message</label>
                    <textarea rows="5" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"></textarea>
                </div>
                @php $buttonClasses = content('__SLUG__', 'button_classes', 'w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'content'); @endphp
                <button type="submit" class="{{ $buttonClasses }}">
                    {{ content('__SLUG__', 'button_label', 'Send Message', 'text', 'content') }}
                </button>
            </form>
            <div class="space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Contact Information</h3>
                    <div class="mt-4 space-y-4 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p>📍 {{ content('__SLUG__', 'address_street', '123 Main Street, Suite 100', 'text', 'contact details') }}<br>{{ content('__SLUG__', 'address_city', 'San Francisco, CA 94105', 'text', 'contact details') }}</p>
                        <p>📞 {{ content('__SLUG__', 'phone', '(555) 123-4567', 'text', 'contact details') }}</p>
                        <p>✉️ {{ content('__SLUG__', 'email', 'hello@example.com', 'text', 'contact details') }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Business Hours</h3>
                    <div class="mt-4 space-y-1 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p>{{ content('__SLUG__', 'hours_weekday', 'Monday–Friday: 9am–6pm PST', 'text', 'contact details') }}</p>
                        <p>{{ content('__SLUG__', 'hours_saturday', 'Saturday: 10am–4pm PST', 'text', 'contact details') }}</p>
                        <p>{{ content('__SLUG__', 'hours_sunday', 'Sunday: Closed', 'text', 'contact details') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{--
@name Hero - Split
@description Two-column hero with text on the left and image placeholder on the right.
@sort 20
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">
                {{ content('__SLUG__', 'headline', 'Build Something Amazing') }}
            </h1>
            <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">
                {{ content('__SLUG__', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a
                    href="{{ content('__SLUG__', 'primary_cta_url', '#') }}"
                    @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
                >
                    {{ content('__SLUG__', 'primary_cta', 'Start Free Trial') }}
                </a>
                @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle'))
                <a
                    href="{{ content('__SLUG__', 'secondary_cta_url', '#') }}"
                    @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                    class="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors"
                >
                    {{ content('__SLUG__', 'secondary_cta', 'Watch Demo →') }}
                </a>
                @endif
            </div>
        </div>
        <div class="rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
            @php $heroImage = content('__SLUG__', 'image', '', 'image'); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('__SLUG__', 'image_alt', '') }}" class="w-full h-full object-cover">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
    </div>
</section>

{{--
@name Hero - Centered
@description Full-width centered hero with headline, subheadline, and dual CTA buttons.
@sort 10
--}}
<section class="py-24 px-6 bg-white dark:bg-zinc-900 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">{{ content('__SLUG__', 'badge', 'Welcome', 'text', 'content') }}</span>
        <h1 class="text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight">
            {{ content('__SLUG__', 'headline', 'Your Headline Goes Here', 'text', 'content') }}
        </h1>
        <p class="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed">
            {{ content('__SLUG__', 'subheadline', 'A compelling subheadline that explains what you do and why it matters to your audience.', 'text', 'content') }}
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'call to action') }}"
                @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle', 'call to action')) target="_blank" rel="noopener noreferrer" @endif
                class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            >
                {{ content('__SLUG__', 'primary_cta', 'Get Started', 'text', 'call to action') }}
            </a>
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'call to action'))
            <a
                href="{{ content('__SLUG__', 'secondary_cta_url', '#', 'text', 'call to action') }}"
                @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle', 'call to action')) target="_blank" rel="noopener noreferrer" @endif
                class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
            >
                {{ content('__SLUG__', 'secondary_cta', 'Learn More', 'text', 'call to action') }}
            </a>
            @endif
        </div>
    </div>
</section>

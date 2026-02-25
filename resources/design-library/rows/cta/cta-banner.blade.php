{{--
@name CTA - Banner
@description Full-width call-to-action banner with headline and button.
@sort 10
--}}
<section class="bg-primary py-16 px-6 text-center">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-4xl font-bold text-white">{{ content('__SLUG__', 'headline', 'Ready to Get Started?') }}</h2>
        <p class="mt-4 text-lg text-white/80">{{ content('__SLUG__', 'subheadline', 'Join thousands of satisfied customers today.') }}</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ content('__SLUG__', 'primary_cta_url', '#') }}"
                @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                class="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            >
                {{ content('__SLUG__', 'primary_cta', 'Start Free Trial') }}
            </a>
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle'))
            <a
                href="{{ content('__SLUG__', 'secondary_cta_url', '#') }}"
                @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                class="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors"
            >
                {{ content('__SLUG__', 'secondary_cta', 'Talk to Sales') }}
            </a>
            @endif
        </div>
    </div>
</section>

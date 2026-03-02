{{--
@name Login - Card
@description Centered login card with email and password fields.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6 py-12', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'w-full max-w-sm', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-8', 'classes', 'content'); @endphp
        @php $brandClasses = content('__SLUG__', 'brand_classes', 'text-2xl font-bold text-zinc-900 dark:text-white', 'classes', 'content'); @endphp
        @php $cardClasses = content('__SLUG__', 'card_classes', 'bg-white dark:bg-zinc-900 rounded-card shadow-card border border-zinc-200 dark:border-zinc-700 p-8', 'classes', 'content'); @endphp
        @php $labelClasses = content('__SLUG__', 'label_classes', 'block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1', 'classes', 'content'); @endphp
        @php $inputClasses = content('__SLUG__', 'input_classes', 'w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition', 'classes', 'content'); @endphp
        @php $forgotPasswordClasses = content('__SLUG__', 'forgot_password_classes', 'text-xs text-primary hover:text-primary/80', 'classes', 'content'); @endphp
        @php $submitButtonClasses = content('__SLUG__', 'submit_button_classes', 'w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'content'); @endphp
        @php $signupTextClasses = content('__SLUG__', 'signup_text_classes', 'mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400', 'classes', 'content'); @endphp
        @php $signupLinkClasses = content('__SLUG__', 'signup_link_classes', 'text-primary font-medium hover:text-primary/80', 'classes', 'content'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            <a href="/" class="{{ $brandClasses }}">{{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}</a>
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Welcome back', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading mt-4 text-xl font-semibold text-zinc-800 dark:text-zinc-100', 'classes', 'headline'); @endphp
            <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Sign in to your account', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-1 text-sm text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        <div class="{{ $cardClasses }}">
            <form class="space-y-5">
                <div>
                    <label class="{{ $labelClasses }}">Email</label>
                    <input type="email" class="{{ $inputClasses }}" />
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="{{ $labelClasses }}">Password</label>
                        <a href="#" class="{{ $forgotPasswordClasses }}">Forgot password?</a>
                    </div>
                    <input type="password" class="{{ $inputClasses }}" />
                </div>
                <button type="submit" class="{{ $submitButtonClasses }}">
                    Sign In
                </button>
            </form>
        </div>
        <p class="{{ $signupTextClasses }}">
            Don't have an account? <a href="#" class="{{ $signupLinkClasses }}">Sign up</a>
        </p>
    </div>
</section>

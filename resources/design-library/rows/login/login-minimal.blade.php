{{--
@name Login - Minimal
@description Ultra-minimal login form with no card, full screen.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="w-full max-w-sm">
    <x-dl.wrapper slug="__SLUG__" prefix="form_inner"
        default-classes="">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Sign In"
            default-tag="h1"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-8" />
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="mb-4">
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="Email address"
                default-classes="w-full border-b border-zinc-300 dark:border-zinc-600 bg-transparent px-0 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-primary transition-colors" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_password"
            default-classes="mb-8">
            <x-dl.wrapper slug="__SLUG__" prefix="input_password" tag="input"
                type="password" placeholder="Password"
                default-classes="w-full border-b border-zinc-300 dark:border-zinc-600 bg-transparent px-0 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-primary transition-colors" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Continue
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="footer_links"
            default-classes="mt-6 flex items-center justify-between text-sm">
            <x-dl.link slug="__SLUG__" prefix="forgot_link"
                default-label="Forgot password?"
                default-url="/forgot-password"
                default-classes="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors" />
            <x-dl.link slug="__SLUG__" prefix="register_link"
                default-label="Create account"
                default-url="/register"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

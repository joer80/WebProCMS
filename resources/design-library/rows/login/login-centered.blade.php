{{--
@name Login - Centered
@description Standard centered login card with brand logo and social proof.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6 py-12"
    default-container-classes="w-full max-w-sm">
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-card p-8 shadow-card">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-8">
            <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                href="/"
                default-classes="text-2xl font-bold text-zinc-900 dark:text-white">
                <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                    default-classes="" />
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Sign in to continue"
                default-tag="h1"
                default-classes="font-heading mt-4 text-xl font-semibold text-zinc-900 dark:text-white" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="mb-4">
            <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Email
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="you@example.com"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_password"
            default-classes="mb-2">
            <x-dl.wrapper slug="__SLUG__" prefix="label_password" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Password
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="input_password" tag="input"
                type="password" placeholder="••••••••"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="forgot_link_wrapper"
            default-classes="text-right mb-5">
            <x-dl.link slug="__SLUG__" prefix="forgot_link"
                default-label="Forgot password?"
                default-url="/forgot-password"
                default-classes="text-sm text-primary hover:text-primary/80 transition-colors" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Sign In
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="register_link" tag="p"
            default-classes="mt-6 text-sm text-center text-zinc-500 dark:text-zinc-400">
            New here?
            <x-dl.link slug="__SLUG__" prefix="register_cta"
                default-label="Create a free account"
                default-url="/register"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

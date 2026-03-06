{{--
@name Login - Dark
@description Dark themed full-screen login with card.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-950 flex items-center justify-center px-6 py-12"
    default-container-classes="w-full max-w-sm">
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="w-full bg-zinc-900 border border-zinc-800 rounded-card p-8">
        <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
            href="/"
            default-classes="block text-center text-2xl font-bold text-white mb-8">
            <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                default-classes="" />
        </x-dl.wrapper>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Welcome back"
            default-tag="h1"
            default-classes="font-heading text-xl font-semibold text-white text-center mb-6" />
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="mb-4">
            <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
                default-classes="block text-sm font-medium text-zinc-300 mb-1.5">
                Email
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="you@example.com"
                default-classes="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_password"
            default-classes="mb-6">
            <x-dl.wrapper slug="__SLUG__" prefix="label_password" tag="label"
                default-classes="block text-sm font-medium text-zinc-300 mb-1.5">
                Password
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="input_password" tag="input"
                type="password" placeholder="••••••••"
                default-classes="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Sign In
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="footer_links"
            default-classes="mt-6 flex items-center justify-between text-sm">
            <x-dl.link slug="__SLUG__" prefix="forgot_link"
                default-label="Forgot password?"
                default-url="/forgot-password"
                default-classes="text-zinc-500 hover:text-zinc-300 transition-colors" />
            <x-dl.link slug="__SLUG__" prefix="register_link"
                default-label="Create account"
                default-url="/register"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

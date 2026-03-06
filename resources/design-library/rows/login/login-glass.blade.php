{{--
@name Login - Glass
@description Login card with glassmorphism effect over a gradient background.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen flex items-center justify-center px-6 py-12 bg-gradient-to-br from-primary/80 to-accent/60"
    default-container-classes="w-full max-w-sm">
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="w-full bg-white/20 backdrop-blur-xl border border-white/30 rounded-card p-8 shadow-xl">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-8">
            <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                href="/"
                default-classes="text-2xl font-bold text-white block mb-2">
                <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                    default-classes="" />
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Sign In"
                default-tag="h1"
                default-classes="font-heading text-lg font-semibold text-white/90" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="mb-4">
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="Email address"
                default-classes="w-full rounded-lg border border-white/30 bg-white/20 px-4 py-2.5 text-sm text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_password"
            default-classes="mb-6">
            <x-dl.wrapper slug="__SLUG__" prefix="input_password" tag="input"
                type="password" placeholder="Password"
                default-classes="w-full rounded-lg border border-white/30 bg-white/20 px-4 py-2.5 text-sm text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-white/90 transition-colors">
            Sign In
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="register_link" tag="p"
            default-classes="mt-6 text-sm text-center text-white/70">
            No account?
            <x-dl.link slug="__SLUG__" prefix="register_cta"
                default-label="Sign up"
                default-url="/register"
                default-classes="text-white font-semibold hover:underline" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

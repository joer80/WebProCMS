{{--
@name Login - Split
@description Two-column login with image/branding panel on the left.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900"
    default-container-classes="">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 min-h-screen">
        <x-dl.image slug="__SLUG__" prefix="side_image"
            default-wrapper-classes="hidden md:block overflow-hidden bg-primary"
            default-image-classes="w-full h-full object-cover opacity-80" />
        <x-dl.wrapper slug="__SLUG__" prefix="form_panel"
            default-classes="flex flex-col justify-center px-8 md:px-16 py-16">
            <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                href="/"
                default-classes="text-2xl font-bold text-zinc-900 dark:text-white mb-8 block">
                <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                    default-classes="" />
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Sign in to your account"
                default-tag="h1"
                default-classes="font-heading text-2xl font-bold text-zinc-900 dark:text-white mb-2" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Enter your credentials below."
                default-classes="text-zinc-500 dark:text-zinc-400 mb-8" />
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
                default-classes="mb-6">
                <x-dl.wrapper slug="__SLUG__" prefix="label_password" tag="label"
                    default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Password
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="input_password" tag="input"
                    type="password" placeholder="••••••••"
                    default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
                type="submit"
                default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Sign In
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="register_link" tag="p"
                default-classes="mt-6 text-sm text-center text-zinc-500 dark:text-zinc-400">
                Don't have an account?
                <x-dl.link slug="__SLUG__" prefix="register_cta"
                    default-label="Sign up"
                    default-url="/register"
                    default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

{{--
@name Login - Card
@description Centered login card with email and password fields.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6 py-12"
    default-container-classes="w-full max-w-sm">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-8">
            <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                href="/"
                default-classes="text-2xl font-bold text-zinc-900 dark:text-white">
                <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                    default-classes="" />
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Welcome back"
                default-tag="h1"
                default-classes="font-heading mt-4 text-xl font-semibold text-zinc-800 dark:text-zinc-100" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Sign in to your account"
                default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="card"
            default-classes="bg-white dark:bg-zinc-900 rounded-card shadow-card border border-zinc-200 dark:border-zinc-700 p-8">
            <x-dl.group slug="__SLUG__" prefix="form" tag="form"
                default-classes="space-y-5">
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="label" tag="label"
                        default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Email
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="input" tag="input"
                        type="email"
                        default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <div>
                    <x-dl.group slug="__SLUG__" prefix="password_row"
                        default-classes="flex items-center justify-between mb-1">
                        <x-dl.wrapper slug="__SLUG__" prefix="label" tag="label"
                            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Password
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="forgot_password" tag="a"
                            href="#"
                            default-classes="text-xs text-primary hover:text-primary/80">
                            Forgot password?
                        </x-dl.wrapper>
                    </x-dl.group>
                    <x-dl.wrapper slug="__SLUG__" prefix="input" tag="input"
                        type="password"
                        default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <x-dl.wrapper slug="__SLUG__" prefix="submit_button" tag="button"
                    type="submit"
                    default-classes="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                    Sign In
                </x-dl.wrapper>
            </x-dl.group>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="signup_text" tag="p"
            default-classes="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
            Don't have an account? <x-dl.wrapper slug="__SLUG__" prefix="signup_link" tag="a"
                href="#"
                default-classes="text-primary font-medium hover:text-primary/80">Sign up</x-dl.wrapper>
        </x-dl.wrapper>
</x-dl.section>

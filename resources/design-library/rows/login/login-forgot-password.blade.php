{{--
@name Login - Forgot Password
@description Password reset request form.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6 py-12"
    default-container-classes="w-full max-w-sm">
    <x-dl.wrapper slug="__SLUG__" prefix="form_card"
        default-classes="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-card p-8 shadow-card">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-8">
            <x-dl.icon slug="__SLUG__" prefix="lock_icon" name="lock-closed"
                default-wrapper-classes="mb-4 text-primary"
                default-classes="size-10 mx-auto" />
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Forgot your password?"
                default-tag="h1"
                default-classes="font-heading text-xl font-semibold text-zinc-900 dark:text-white mb-2" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Enter your email and we'll send you a reset link."
                default-classes="text-sm text-zinc-500 dark:text-zinc-400" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="field_email"
            default-classes="mb-6">
            <x-dl.wrapper slug="__SLUG__" prefix="label_email" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Email address
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="input_email" tag="input"
                type="email" placeholder="you@example.com"
                default-classes="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-transparent px-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/40" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="submit_btn" tag="button"
            type="submit"
            default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Send Reset Link
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="back_link" tag="p"
            default-classes="mt-6 text-sm text-center">
            <x-dl.link slug="__SLUG__" prefix="login_cta"
                default-label="← Back to sign in"
                default-url="/login"
                default-classes="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

{{--
@name Login - Two Panel
@description Login with branding/features on the left, form on the right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 min-h-screen">
        <x-dl.wrapper slug="__SLUG__" prefix="brand_panel"
            default-classes="hidden md:flex flex-col justify-center px-16 bg-primary">
            <x-dl.wrapper slug="__SLUG__" prefix="brand_name" tag="div"
                default-classes="text-3xl font-bold text-white mb-6">
                Your Brand
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="brand_tagline" tag="p" default="The platform that helps teams ship better products, faster."
                default-classes="text-xl text-white/80 mb-10 leading-relaxed" />
            <x-dl.grid slug="__SLUG__" prefix="brand_features"
                default-grid-classes="space-y-4"
                default-items='[{"feature":"Lightning fast setup in under 5 minutes"},{"feature":"Trusted by 10,000+ teams worldwide"},{"feature":"SOC 2 Type II certified and secure"}]'>
                @dlItems('__SLUG__', 'brand_features', $brandFeatures, '[{"feature":"Lightning fast setup in under 5 minutes"},{"feature":"Trusted by 10,000+ teams worldwide"},{"feature":"SOC 2 Type II certified and secure"}]')
                @foreach ($brandFeatures as $item)
                    <x-dl.card slug="__SLUG__" prefix="brand_feature"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center gap-3 text-white/90 text-sm">
                        <x-dl.icon slug="__SLUG__" prefix="feature_check" name="check-circle:solid"
                            default-classes="size-5 text-white shrink-0" />
                        {{ $item['feature'] }}
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="form_panel"
            default-classes="flex flex-col justify-center px-8 md:px-16 py-16 bg-white dark:bg-zinc-900">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Sign in"
                default-tag="h1"
                default-classes="font-heading text-2xl font-bold text-zinc-900 dark:text-white mb-2" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Welcome back! Please enter your details."
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
                default-classes="text-right mb-6">
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
                Don't have an account?
                <x-dl.link slug="__SLUG__" prefix="register_cta"
                    default-label="Sign up free"
                    default-url="/register"
                    default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

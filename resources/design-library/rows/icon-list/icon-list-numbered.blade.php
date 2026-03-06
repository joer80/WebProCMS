{{--
@name Icon List - Numbered
@description Numbered list with step numbers, title, and description.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="How It Works"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="steps"
        default-grid-classes="space-y-8"
        default-items='[{"number":"01","title":"Sign Up","desc":"Create your account in seconds. No credit card required to get started."},{"number":"02","title":"Configure","desc":"Set up your workspace with our guided onboarding wizard."},{"number":"03","title":"Invite Your Team","desc":"Add team members and assign roles with one click."},{"number":"04","title":"Launch","desc":"Start your first project and see results from day one."}]'>
        @dlItems('__SLUG__', 'steps', $steps, '[{"number":"01","title":"Sign Up","desc":"Create your account in seconds. No credit card required to get started."},{"number":"02","title":"Configure","desc":"Set up your workspace with our guided onboarding wizard."},{"number":"03","title":"Invite Your Team","desc":"Add team members and assign roles with one click."},{"number":"04","title":"Launch","desc":"Start your first project and see results from day one."}]')
        @foreach ($steps as $step)
            <x-dl.card slug="__SLUG__" prefix="step_item"
                default-classes="flex items-start gap-6">
                <x-dl.wrapper slug="__SLUG__" prefix="step_number"
                    default-classes="shrink-0 text-4xl font-black text-zinc-200 dark:text-zinc-700 leading-none w-12">
                    {{ $step['number'] }}
                </x-dl.wrapper>
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="step_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white text-lg">
                        {{ $step['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="step_desc" tag="p"
                        default-classes="mt-1 text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $step['desc'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

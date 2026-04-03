{{--
@name Features - Numbered Steps
@description Three-column numbered steps with large number, title, and description.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Get Started in Three Steps"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Simple, fast, and designed to get you results from day one."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="steps"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"step":"01","title":"Create Your Account","desc":"Sign up in seconds with just your email. No credit card required to get started."},{"step":"02","title":"Set Up Your Workspace","desc":"Follow the guided setup to configure your workspace and invite your team."},{"step":"03","title":"Start Building","desc":"Use our templates and tools to launch your first project in minutes."}]'>
        @dlItems('__SLUG__', 'steps', $steps, '[{"step":"01","title":"Create Your Account","desc":"Sign up in seconds with just your email. No credit card required to get started."},{"step":"02","title":"Set Up Your Workspace","desc":"Follow the guided setup to configure your workspace and invite your team."},{"step":"03","title":"Start Building","desc":"Use our templates and tools to launch your first project in minutes."}]')
        @foreach ($steps as $step)
            <x-dl.card slug="__SLUG__" prefix="step_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                <x-dl.wrapper slug="__SLUG__" prefix="step_number"
                    default-classes="text-5xl font-black text-zinc-200 dark:text-zinc-700 mb-4 leading-none">
                    {{ $step['step'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="step_title" tag="h3"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $step['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="step_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    {{ $step['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

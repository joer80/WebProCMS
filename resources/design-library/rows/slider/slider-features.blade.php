{{--
@name Slider - Feature Tabs
@description Tabbed feature showcase with icon, heading, and description per tab.
@sort 60
--}}
@dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Performance","desc":"Our global edge network delivers sub-100ms load times in every region, so your users never wait."},{"icon":"shield-check","title":"Security","desc":"End-to-end encryption, zero-trust architecture, and SOC 2 Type II certification keep your data safe."},{"icon":"chart-bar","title":"Analytics","desc":"Real-time dashboards and custom reports give you visibility into every metric that matters."}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-5xl mx-auto"
    x-data="{ active: 0 }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Why Teams Choose Us"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="tabs_wrapper"
        default-classes="grid md:grid-cols-3 gap-2 mb-10">
        @foreach ($features as $i => $feature)
            <x-dl.card slug="__SLUG__" prefix="tab_button" tag="button"
                default-classes="p-4 rounded-lg text-left transition-all border"
                @click="active = {{ $i }}"
                :class="active === {{ $i }} ? 'bg-white dark:bg-zinc-900 border-primary shadow-card' : 'border-transparent hover:bg-white/50 dark:hover:bg-zinc-900/50'">
                <x-dl.icon slug="__SLUG__" prefix="tab_icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mb-2 text-primary"
                    default-classes="size-5" />
                <x-dl.wrapper slug="__SLUG__" prefix="tab_title" tag="span"
                    default-classes="block font-semibold text-zinc-900 dark:text-white text-sm">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="content_wrapper"
        default-classes="bg-white dark:bg-zinc-900 rounded-card p-8 border border-zinc-200 dark:border-zinc-700">
        @foreach ($features as $i => $feature)
            <div x-show="active === {{ $i }}"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0">
                <x-dl.wrapper slug="__SLUG__" prefix="content_title" tag="h3"
                    default-classes="text-2xl font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="content_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed text-lg">
                    {{ $feature['desc'] }}
                </x-dl.wrapper>
            </div>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>

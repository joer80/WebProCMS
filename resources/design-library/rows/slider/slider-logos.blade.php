{{--
@name Slider - Logo Slider
@description Auto-scrolling partner and client logo strip.
@sort 40
--}}
@dlItems('__SLUG__', 'logos', $logos, '[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt"},{"name":"StartupXYZ"},{"name":"Innovate Inc"},{"name":"Future Labs"},{"name":"CloudBase"},{"name":"NextGen"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 overflow-hidden"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="label" default="Our customers"
        default-classes="text-center text-sm text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-8" />
    <x-dl.wrapper slug="__SLUG__" prefix="marquee_wrapper"
        default-classes="relative overflow-hidden"
        x-data="{}"
        x-init="(() => {
            const el = $el.querySelector('[data-marquee]');
            if (el) {
                el.innerHTML += el.innerHTML;
            }
        })()">
        <x-dl.wrapper slug="__SLUG__" prefix="marquee_track"
            default-classes="flex gap-12 items-center animate-[marquee_20s_linear_infinite] whitespace-nowrap"
            data-marquee="">
            @foreach ($logos as $logo)
                <x-dl.card slug="__SLUG__" prefix="logo_item"
                    default-classes="inline-flex items-center shrink-0">
                    <x-dl.wrapper slug="__SLUG__" prefix="logo_name" tag="span"
                        default-classes="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">
                        {{ $logo['name'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

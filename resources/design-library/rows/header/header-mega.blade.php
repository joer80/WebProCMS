{{--
@name Header - Mega Menu
@description Header with an Alpine.js powered full-width mega dropdown menu.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    x-data="{ megaOpen: false, mobileOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 20"
    x-bind:class="scrolled ? 'h-16' : 'h-20'"
    default-sticky="1"
    default-section-classes="z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 transition-all duration-300"
    default-container-classes="max-w-6xl mx-auto px-6 h-full flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <nav class="hidden md:flex items-center gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="link_products" tag="button"
            default-classes="flex items-center gap-1 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            @click="megaOpen = !megaOpen"
            @keydown.escape.window="megaOpen = false">
            Products
            <x-dl.icon slug="__SLUG__" prefix="mega_chevron" name="chevron-down"
                default-classes="size-4 transition-transform duration-200"
                x-bind:class="megaOpen ? 'rotate-180' : ''" />
        </x-dl.wrapper>
        <x-dl.nav slug="__SLUG__" prefix="main_nav"
            default-menu="main-navigation"
            default-classes="flex items-center gap-8"
            default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
    </nav>
    <x-dl.wrapper slug="__SLUG__" prefix="header_button"
        default-classes="flex items-center gap-3">
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
            default-label="Get Started"
            default-url="#"
            default-classes="hidden md:inline-flex px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
        <x-dl.group slug="__SLUG__" prefix="mobile_btn" tag="button"
            default-classes="md:hidden p-1 rounded text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            @click="mobileOpen = !mobileOpen"
            aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" style="display:none;" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </x-dl.group>
    </x-dl.wrapper>
    <div x-show="megaOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        @click.outside="megaOpen = false"
        style="display:none;"
        class="absolute top-full left-0 right-0 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg">
        <x-dl.wrapper slug="__SLUG__" prefix="mega_panel"
            default-classes="max-w-6xl mx-auto px-6 py-8 grid md:grid-cols-3 gap-6">
            <x-dl.grid slug="__SLUG__" prefix="mega_items"
                default-grid-classes="contents"
                default-items='[{"icon":"bolt","title":"Performance","desc":"Global edge delivery for fast load times."},{"icon":"shield-check","title":"Security","desc":"Zero-trust, end-to-end encryption."},{"icon":"chart-bar","title":"Analytics","desc":"Real-time insights for every metric."}]'>
                @dlItems('__SLUG__', 'mega_items', $megaItems, '[{"icon":"bolt","title":"Performance","desc":"Global edge delivery for fast load times."},{"icon":"shield-check","title":"Security","desc":"Zero-trust, end-to-end encryption."},{"icon":"chart-bar","title":"Analytics","desc":"Real-time insights for every metric."}]')
                @foreach ($megaItems as $item)
                    <x-dl.card slug="__SLUG__" prefix="mega_item"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex gap-4 p-4 rounded-card hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <x-dl.icon slug="__SLUG__" prefix="mega_icon" name="{{ $item['icon'] }}"
                            default-wrapper-classes="mt-0.5 text-primary shrink-0"
                            default-classes="size-5" />
                        <x-dl.group slug="__SLUG__" prefix="mega_text"
                            default-classes="">
                            <x-dl.wrapper slug="__SLUG__" prefix="mega_title" tag="h4"
                                default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                                {{ $item['title'] }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="mega_desc" tag="p"
                                default-classes="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $item['desc'] }}
                            </x-dl.wrapper>
                        </x-dl.group>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="mobile_panel"
        x-show="mobileOpen"
        x-transition
        style="display:none;"
        default-classes="absolute top-full left-0 right-0 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg px-6 py-4 flex flex-col">
        <x-dl.nav slug="__SLUG__" prefix="mobile_nav"
            default-menu="main-navigation"
            default-classes="flex flex-col"
            default-item-classes="block py-3 text-base font-medium text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white border-b border-zinc-100 dark:border-zinc-800 last:border-0 transition-colors" />
        <x-dl.link slug="__SLUG__" prefix="mobile_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
            default-label="Get Started"
            default-url="#"
            default-classes="mt-4 inline-flex px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>

@blaze
@props(['slug', 'prefix' => 'nav', 'defaultMenu' => 'main-navigation', 'defaultClasses' => '', 'defaultItemClasses' => '', 'defaultActiveItemClasses' => '', 'defaultDropdownClasses' => '', 'defaultDropdownItemClasses' => ''])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$menuSlug = content($slug, "{$prefix}_menu", $defaultMenu);
$navClasses = content($slug, "{$prefix}_classes", $defaultClasses);
$itemClasses = content($slug, "{$prefix}_item_classes", $defaultItemClasses);
$activeItemClasses = content($slug, "{$prefix}_active_item_classes", $defaultActiveItemClasses);
$dropdownClasses = content($slug, "{$prefix}_dropdown_classes", $defaultDropdownClasses ?: 'absolute top-full left-0 z-50 mt-7 min-w-48 rounded-b-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900 py-1');
$dropdownItemClasses = content($slug, "{$prefix}_dropdown_item_classes", $defaultDropdownItemClasses ?: 'block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors');
$navId = content($slug, "{$prefix}_id", '');
$navAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrsStr = ' data-editor-group="' . e($prefix) . '"';
$extraAttrsStr .= $navId ? ' id="' . e($navId) . '"' : '';
foreach ($navAttrsRaw as $attr) {
    if (! empty($attr['name'])) {
        $extraAttrsStr .= ' ' . e($attr['name']) . '="' . e($attr['value'] ?? '') . '"';
    }
}
$menu = collect(\App\Models\Setting::get('navigation.menus', []))->firstWhere('slug', $menuSlug);
$navItems = array_filter($menu['items'] ?? [], fn ($item) => $item['active'] ?? true);

// Resolve dynamic items into children arrays
$navItems = collect($navItems)->map(function ($item) {
    if (($item['type'] ?? null) === 'dynamic') {
        $expanded = \App\Services\MenuService::expand($item);
        $item['_children'] = $expanded['children'];
        $item['_see_all_url'] = $expanded['see_all_url'];
    }
    return $item;
})->all();

// Group depth-1 items under their preceding top-level parent
$grouped = [];
foreach ($navItems as $item) {
    if (($item['depth'] ?? 0) >= 1) {
        if (! empty($grouped)) {
            $grouped[array_key_last($grouped)]['_sub_children'][] = $item;
        }
        // Orphaned sub-items (no parent above) are skipped
    } else {
        $item['_sub_children'] = [];
        $grouped[] = $item;
    }
}
$navItems = $grouped;
@endphp
@if($toggle)
<nav {{ $attributes }} class="{{ $navClasses }}"{!! $extraAttrsStr !!}>
    @foreach($navItems as $item)
        @if(($item['type'] ?? null) === 'mega')
            @php $megaColumns = $item['columns'] ?? []; $megaColCount = count($megaColumns); @endphp
            <div class="relative inline-flex items-center"
                 x-data="{ megaOpen: false }"
                 @keydown.escape.window="megaOpen = false"
                 x-on:mega-close-others.window="if ($event.detail.id !== '{{ $loop->index }}') megaOpen = false">
                <button type="button"
                        @click="megaOpen = !megaOpen; if (megaOpen) $dispatch('mega-close-others', { id: '{{ $loop->index }}' })"
                        @click.outside="megaOpen = false"
                        class="{{ $itemClasses }} inline-flex cursor-pointer items-center gap-1.5">
                    {{ $item['label'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" :class="megaOpen ? 'rotate-180' : ''" class="size-3 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </button>
                <div x-show="megaOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display:none; position:fixed; left:max(0px, calc((100vw - var(--width-container)) / 2)); right:max(0px, calc((100vw - var(--width-container)) / 2));"
                     x-init="$watch('megaOpen', val => {
                         if (val) { $el.style.top = $el.previousElementSibling.getBoundingClientRect().bottom + 'px'; }
                     })"
                     class="z-50 mt-7 border-x border-b border-zinc-200 dark:border-zinc-800 border-t-[5px] border-t-zinc-400 bg-white dark:bg-zinc-900 shadow-lg rounded-b-xl">
                    <div class="px-6 py-8 grid gap-8"
                         style="grid-template-columns: repeat({{ max(1, $megaColCount) }}, minmax(0, 1fr))">
                        @foreach($megaColumns as $column)
                            <div>
                                @if(!empty($column['heading']))
                                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $column['heading'] }}</p>
                                @endif
                                <div class="space-y-1">
                                    @foreach(($column['links'] ?? []) as $link)
                                        <a href="{{ $link['url'] ?? '#' }}"
                                           @if(!empty($link['new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                                           class="flex gap-3 rounded-lg p-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                            @if(!empty($link['icon']))
                                                @if(str_starts_with($link['icon'], 'ion:'))
                                                    <x-ionicon :name="substr($link['icon'], 4)" class="mt-0.5 size-5 shrink-0 text-primary" />
                                                @else
                                                    <x-heroicon :name="$link['icon']" variant="outline" class="mt-0.5 size-5 shrink-0 text-primary" />
                                                @endif
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $link['title'] }}</p>
                                                @if(!empty($link['desc']))
                                                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $link['desc'] }}</p>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(isset($item['_children']))
            <div class="relative inline-flex items-center" x-data="{ open: false }">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="{{ $itemClasses }} inline-flex cursor-pointer items-center gap-1.5">
                    {{ $item['label'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" :class="open ? 'rotate-180' : ''" class="size-3 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </button>
                <div x-show="open" style="display:none;" class="{{ $dropdownClasses }}">
                    @foreach($item['_children'] as $child)
                        <a href="{{ $child['url'] }}" class="{{ $dropdownItemClasses }}">{{ $child['label'] }}</a>
                    @endforeach
                    @if(!empty($item['show_all']) && !empty($item['_see_all_url']))
                        <div class="my-1 border-t border-zinc-100 dark:border-zinc-800"></div>
                        <a href="{{ $item['_see_all_url'] }}" class="{{ $dropdownItemClasses }} font-medium">
                            {{ $item['show_all_label'] ?? 'See All' }} →
                        </a>
                    @endif
                </div>
            </div>
        @elseif(!empty($item['_sub_children']))
            <div class="relative inline-flex items-center" x-data="{ open: false }">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="{{ $itemClasses }} inline-flex cursor-pointer items-center gap-1.5">
                    {{ $item['label'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" :class="open ? 'rotate-180' : ''" class="size-3 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </button>
                <div x-show="open" style="display:none;" class="{{ $dropdownClasses }}">
                    @foreach($item['_sub_children'] as $child)
                        @php
                            $childHref = isset($child['route']) && Route::has($child['route']) ? route($child['route']) : ($child['url'] ?? '#');
                            $childIsActive = $activeItemClasses && $childHref !== '#' && (
                                isset($child['route']) && Route::has($child['route'])
                                    ? request()->routeIs($child['route'])
                                    : request()->url() === url($childHref)
                            );
                        @endphp
                        <a href="{{ $childHref }}"
                           @if(!empty($child['new_window'])) target="_blank" rel="noopener noreferrer" @endif
                           class="{{ $dropdownItemClasses }}{{ $childIsActive ? ' font-semibold' : '' }}">{{ $child['label'] }}</a>
                    @endforeach
                </div>
            </div>
        @else
            @php
                $href = isset($item['route']) && Route::has($item['route']) ? route($item['route']) : ($item['url'] ?? '#');
                $isActive = $activeItemClasses && $href !== '#' && (
                    isset($item['route']) && Route::has($item['route'])
                        ? request()->routeIs($item['route'])
                        : request()->url() === url($href)
                );
            @endphp
            <a href="{{ $href }}"
               @if(!empty($item['new_window'])) target="_blank" rel="noopener noreferrer" @endif
               class="{{ $itemClasses }}{{ $isActive ? ' ' . $activeItemClasses : '' }}">{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
@endif

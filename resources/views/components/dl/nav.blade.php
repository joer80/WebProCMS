@blaze
@props(['slug', 'prefix' => 'nav', 'defaultMenu' => 'main-navigation', 'defaultClasses' => '', 'defaultItemClasses' => '', 'defaultActiveItemClasses' => ''])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$menuSlug = content($slug, "{$prefix}_menu", $defaultMenu);
$navClasses = content($slug, "{$prefix}_classes", $defaultClasses);
$itemClasses = content($slug, "{$prefix}_item_classes", $defaultItemClasses);
$activeItemClasses = content($slug, "{$prefix}_active_item_classes", $defaultActiveItemClasses);
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
@endphp
@if($toggle)
<nav {{ $attributes }} class="{{ $navClasses }}"{!! $extraAttrsStr !!}>
    @foreach($navItems as $item)
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
    @endforeach
</nav>
@endif

@blaze
@props(['slug', 'prefix' => 'nav', 'defaultMenu' => 'main-navigation', 'defaultClasses' => '', 'defaultItemClasses' => ''])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$menuSlug = content($slug, "{$prefix}_menu", $defaultMenu);
$navClasses = content($slug, "{$prefix}_classes", $defaultClasses);
$itemClasses = content($slug, "{$prefix}_item_classes", $defaultItemClasses);
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
        <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : ($item['url'] ?? '#') }}"
           @if(!empty($item['new_window'])) target="_blank" rel="noopener noreferrer" @endif
           class="{{ $itemClasses }}">{{ $item['label'] }}</a>
    @endforeach
</nav>
@endif

@blaze
@props(['slug', 'prefix' => 'subheadline', 'default' => '', 'defaultClasses' => 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'tag' => 'p'])
@php
$toggle = content($slug, "toggle_{$prefix}", '1');
$text = content($slug, $prefix, $default);
$cls = content($slug, "{$prefix}_classes", $defaultClasses);
$subId = content($slug, "{$prefix}_id", '');
$subAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = $subId ? ['id' => $subId] : [];
foreach ($subAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
@endphp
@if($toggle)
{!! "<{$tag} " . $attributes->merge(array_merge(['class' => $cls], $extraAttrs))->toHtml() . ">" . e($text) . "</{$tag}>" !!}
@endif

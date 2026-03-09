@blaze
@props(['slug', 'defaultWrapperClasses' => 'mt-8 flex flex-wrap items-center gap-4', 'defaultPrimaryLabel' => 'Get Started', 'defaultPrimaryClasses' => 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'defaultSecondaryLabel' => 'Learn More', 'defaultSecondaryClasses' => 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors'])
@php
$wrapperCls = content($slug, 'buttons_wrapper_classes', $defaultWrapperClasses);
$togglePrimary = content($slug, 'toggle_primary_button', '1');
$primaryLabel = content($slug, 'primary_button', $defaultPrimaryLabel);
$primaryCls = content($slug, 'primary_button_classes', $defaultPrimaryClasses);
$primaryUrl = content($slug, 'primary_button_url', '#');
$primaryNewTab = content($slug, 'primary_button_new_tab', '');
$toggleSecondary = content($slug, 'toggle_secondary_button', '1');
$secondaryLabel = content($slug, 'secondary_button', $defaultSecondaryLabel);
$secondaryCls = content($slug, 'secondary_button_classes', $defaultSecondaryClasses);
$secondaryUrl = content($slug, 'secondary_button_url', '#');
$secondaryNewTab = content($slug, 'secondary_button_new_tab', '');
$animPreset = content($slug, 'buttons_animation', '');
$animAttr = '';
if ($animPreset) {
    $animPresets = \App\View\Components\Dl\Section::animationPresets();
    $animDelay = content($slug, 'buttons_animation_delay', '');
    $animClasses = ($animPresets[$animPreset] ?? '') . ($animDelay ? " {$animDelay}" : '');
    $animAttr = " x-data=\"{ animated: false }\" x-intersect.once=\"animated = true\" :class=\"animated ? '{$animClasses}' : 'opacity-0'\"";
}
@endphp
{!! "<div class=\"{$wrapperCls}\" data-editor-group=\"buttons\"{$animAttr}>" !!}
    @if($togglePrimary)
    <a
        href="{{ $primaryUrl }}"
        @if($primaryNewTab) target="_blank" rel="noopener noreferrer" @endif
        class="{{ $primaryCls }}"
        data-editor-group="primary_button"
    >{{ $primaryLabel }}</a>
    @endif
    @if($toggleSecondary)
    <a
        href="{{ $secondaryUrl }}"
        @if($secondaryNewTab) target="_blank" rel="noopener noreferrer" @endif
        class="{{ $secondaryCls }}"
        data-editor-group="secondary_button"
    >{{ $secondaryLabel }}</a>
    @endif
</div>

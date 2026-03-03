@blaze
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
@endphp
<div class="{{ $wrapperCls }}">
    @if($togglePrimary)
    <a
        href="{{ $primaryUrl }}"
        @if($primaryNewTab) target="_blank" rel="noopener noreferrer" @endif
        class="{{ $primaryCls }}"
    >{{ $primaryLabel }}</a>
    @endif
    @if($toggleSecondary)
    <a
        href="{{ $secondaryUrl }}"
        @if($secondaryNewTab) target="_blank" rel="noopener noreferrer" @endif
        class="{{ $secondaryCls }}"
    >{{ $secondaryLabel }}</a>
    @endif
</div>

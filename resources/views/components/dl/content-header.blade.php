@blaze
@php
$wrapperCls = content($slug, 'header_wrapper_classes', $defaultWrapperClasses);
@endphp
<div class="{{ $wrapperCls }}">
    <x-dl-heading
        :slug="$slug"
        prefix="headline"
        :default="$defaultHeading"
        :default-tag="$defaultHeadingTag"
        :default-classes="$defaultHeadingClasses" />
    <x-dl-subheadline
        :slug="$slug"
        prefix="subheadline"
        :default="$defaultSubheadline"
        :default-classes="$defaultSubheadlineClasses" />
</div>

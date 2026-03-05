@blaze
@props(['slug'])
@php
$formId = (int) content($slug, 'form_id', '');
@endphp
@if ($formId)
    @livewire('contact-form', ['formId' => $formId], key("contact-form-{$slug}"))
@endif

<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Get in touch with the GetRows team. We respond within one business day. Ask us about pricing, enterprise plans, or anything else.'])] #[Title('Contact Us — GetRows')] class extends Component {
}; ?>
<div>{{-- ROW:start:contact-form:YsqD1J --}}
<x-dl.section slug="contact-form:YsqD1J"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto grid lg:grid-cols-2 gap-12 items-start">
    <div>
        <x-dl.heading slug="contact-form:YsqD1J" prefix="headline" default="Get in Touch"
            default-tag="h1"
            default-classes="text-4xl font-semibold leading-tight mb-4" />
        <x-dl.subheadline slug="contact-form:YsqD1J" prefix="description" default="Have a question or just want to say hello? Fill out the form and we'll get back to you within one business day."
            default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-8" />
        <x-dl.grid slug="contact-form:YsqD1J" prefix="contact_items"
            default-grid-classes="space-y-6 text-sm"
            default-items='[{"label":"Email","value":"hello@example.com"},{"label":"Phone","value":"(555) 000-0000"},{"label":"Office","value":"123 Main Street, San Francisco CA 94105"},{"label":"Hours","value":"Mon\u2013Fri: 9am\u20136pm"}]'>
            @dlItems('contact-form:YsqD1J', 'contact_items', $contactItems, '[{"label":"Email","value":"hello@example.com"},{"label":"Phone","value":"(555) 000-0000"},{"label":"Office","value":"123 Main Street, San Francisco CA 94105"},{"label":"Hours","value":"Mon\u2013Fri: 9am\u20136pm"}]')
            @foreach ($contactItems as $contactItem)
                <x-dl.card slug="contact-form:YsqD1J" prefix="contact_item" default-classes="">
                    <x-dl.wrapper slug="contact-form:YsqD1J" prefix="item_label" tag="p"
                        default-classes="font-semibold mb-1">{{ $contactItem['label'] }}</x-dl.wrapper>
                    <x-dl.wrapper slug="contact-form:YsqD1J" prefix="item_value" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400">{{ $contactItem['value'] }}</x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </div>
    <x-dl.form-select slug="contact-form:YsqD1J" />
</x-dl.section>
{{-- ROW:end:contact-form:YsqD1J --}}
</div>

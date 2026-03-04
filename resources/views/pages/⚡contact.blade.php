<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Get in touch with the GetRows team. We respond within one business day. Ask us about pricing, enterprise plans, or anything else.'])] #[Title('Contact Us — GetRows')] class extends Component {
    #[Validate('required|string|max:100')]
    public string $firstName = '';

    #[Validate('required|string|max:100')]
    public string $lastName = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:30')]
    public string $phone = '';

    #[Validate('required|string|max:2000')]
    public string $inquiry = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        // TODO: Send email when SMTP is configured.

        $this->submitted = true;
    }
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
    <x-dl.group slug="contact-form:YsqD1J" prefix="form" tag="form"
        default-classes="space-y-5 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
        <x-dl.card slug="contact-form:YsqD1J" prefix="name_grid"
            default-classes="grid grid-cols-2 gap-4">
            <div>
                <x-dl.wrapper slug="contact-form:YsqD1J" prefix="first_name_label" tag="label"
                    default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    First Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="contact-form:YsqD1J" prefix="first_name_input" tag="input"
                    type="text" placeholder="Jane"
                    default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>
            <div>
                <x-dl.wrapper slug="contact-form:YsqD1J" prefix="last_name_label" tag="label"
                    default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Last Name
                </x-dl.wrapper>
                <x-dl.wrapper slug="contact-form:YsqD1J" prefix="last_name_input" tag="input"
                    type="text" placeholder="Smith"
                    default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>
        </x-dl.card>
        <div>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="email_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Email
            </x-dl.wrapper>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="email_input" tag="input"
                type="email" placeholder="jane@example.com"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
        </div>
        <div>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="phone_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Phone Number
            </x-dl.wrapper>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="phone_input" tag="input"
                type="tel" placeholder="+1 (555) 000-0000"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary" />
        </div>
        <div>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="inquiry_label" tag="label"
                default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Your Inquiry
            </x-dl.wrapper>
            <x-dl.wrapper slug="contact-form:YsqD1J" prefix="inquiry_textarea" tag="textarea"
                rows="5" placeholder="Tell us how we can help…"
                default-classes="block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary resize-none">
            </x-dl.wrapper>
        </div>
        <x-dl.button slug="contact-form:YsqD1J" prefix="submit" type="submit" default="Send Message"
            default-classes="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.group>
</x-dl.section>
{{-- ROW:end:contact-form:YsqD1J --}}
</div>

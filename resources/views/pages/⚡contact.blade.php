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

<div>
    <div class="grid lg:grid-cols-2 gap-12">
        {{-- Contact info --}}
        <div>
            <h1 class="text-4xl font-semibold leading-tight mb-4">Get in Touch</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-8">
                Have a question or just want to say hello? Fill out the form and we'll get back to you within one business day.
            </p>

            <div class="space-y-6 text-sm">
                <div>
                    <p class="font-semibold mb-1">Email</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{!! \App\Support\ShortcodeProcessor::process('[[business-email]]') !!}</p>
                </div>
                <div>
                    <p class="font-semibold mb-1">Phone</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{!! \App\Support\ShortcodeProcessor::process('[[business-phone]]') !!}</p>
                </div>
                <div>
                    <p class="font-semibold mb-1">Office</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{!! \App\Support\ShortcodeProcessor::process('[[business-address-street]]') !!}<br>{!! \App\Support\ShortcodeProcessor::process('[[business-address-city-state-zip]]') !!}</p>
                </div>
                <div>
                    <p class="font-semibold mb-1">Hours</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{!! \App\Support\ShortcodeProcessor::process('[[business-hours]]') !!}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
            @if ($submitted)
                <div class="flex flex-col items-center justify-center h-full text-center py-8 gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <flux:icon name="check" class="text-green-600 dark:text-green-400" />
                    </div>
                    <flux:heading size="lg">Message Sent!</flux:heading>
                    <flux:text class="text-[#706f6c] dark:text-[#A1A09A]">
                        Thanks for reaching out. We'll be in touch within one business day.
                    </flux:text>
                    <flux:button wire:click="$set('submitted', false)" variant="ghost">
                        Send another message
                    </flux:button>
                </div>
            @else
                <form wire:submit="submit" class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>First Name</flux:label>
                            <flux:input wire:model="firstName" type="text" placeholder="Jane" required />
                            <flux:error name="firstName" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Last Name</flux:label>
                            <flux:input wire:model="lastName" type="text" placeholder="Smith" required />
                            <flux:error name="lastName" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="jane@example.com" required />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            Phone Number
                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model="phone" type="tel" placeholder="+1 (555) 000-0000" />
                        <flux:error name="phone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Your Inquiry</flux:label>
                        <flux:textarea wire:model="inquiry" placeholder="Tell us how we can help…" rows="5" required />
                        <flux:error name="inquiry" />
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>Send Message</span>
                        <span wire:loading>Sending…</span>
                    </flux:button>
                </form>
            @endif
        </div>
    </div>
</div>

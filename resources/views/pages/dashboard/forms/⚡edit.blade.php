<?php

use App\Enums\SpamProtection;
use App\Models\Form;
use App\Models\Setting;
use App\Rules\CommaSeparatedEmails;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Edit Form')] class extends Component {
    public Form $form;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $notificationEmail = '';

    public bool $saveSubmissions = true;

    public string $spamProtection = 'none';

    /** @var array<string, array{enabled: bool, required: bool, label: string, field_type: string}> */
    public array $fields = [];

    public function mount(Form $form): void
    {
        $this->form = $form;
        $this->name = $form->name;
        $this->notificationEmail = $form->notification_email ?? '';
        $this->saveSubmissions = $form->save_submissions;
        $this->spamProtection = $form->spam_protection?->value ?? 'none';
        $this->fields = array_merge($form->type->defaultFields(), $form->fields ?? []);
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'notificationEmail' => ['nullable', new CommaSeparatedEmails, 'max:500'],
            'spamProtection'    => ['required', 'in:none,honeypot,recaptcha,turnstile'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->form->update([
            'name'               => $this->name,
            'notification_email' => $this->notificationEmail ?: null,
            'save_submissions'   => $this->saveSubmissions,
            'spam_protection'    => $this->spamProtection,
            'fields'             => $this->fields,
        ]);

        $this->redirect(route('dashboard.forms.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.forms.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Form</flux:heading>
            <flux:badge>{{ $form->type->label() }}</flux:badge>
        </div>

        <form wire:submit="save" class="max-w-2xl space-y-6">
            <flux:field>
                <flux:label>Form Name</flux:label>
                <flux:input wire:model="name" type="text" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>
                    Notification Email
                    <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                </flux:label>
                <flux:input wire:model="notificationEmail" type="text" placeholder="e.g. hello@example.com, team@example.com" />
                <flux:description>Separate multiple addresses with commas. An email is sent whenever the form is submitted.</flux:description>
                <flux:error name="notificationEmail" />
            </flux:field>

            <div class="flex items-center gap-3">
                <flux:switch wire:model="saveSubmissions" />
                <div>
                    <flux:label>Save submissions to database</flux:label>
                    <flux:description>Store each submission so you can review them in the dashboard.</flux:description>
                </div>
            </div>

            <div>
                <flux:heading size="lg" class="mb-1">Spam Protection</flux:heading>
                <flux:text class="mb-4 text-zinc-500">Choose how to protect this form from bots and automated submissions.</flux:text>

                @php
                    $recaptchaConfigured = \App\Models\Setting::get('spam.recaptcha_site_key') && \App\Models\Setting::get('spam.recaptcha_secret_key');
                    $turnstileConfigured = \App\Models\Setting::get('spam.turnstile_site_key') && \App\Models\Setting::get('spam.turnstile_secret_key');
                @endphp

                <flux:radio.group wire:model="spamProtection">
                    <flux:radio value="none" label="None" description="No spam protection. Not recommended for public forms." />
                    <flux:radio value="honeypot" label="Honeypot + Rate Limiting" description="Adds a hidden field bots fill in, plus limits submissions to 5 per IP per 10 minutes. No impact on real users." />
                    @if ($recaptchaConfigured)
                        <flux:radio value="recaptcha" label="Google reCAPTCHA v3" description="Invisible score-based verification by Google. No user interaction required." />
                    @else
                        <flux:radio value="recaptcha" label="Google reCAPTCHA v3" description="API keys not configured. Add them in Advanced Settings to enable this option." disabled />
                    @endif
                    @if ($turnstileConfigured)
                        <flux:radio value="turnstile" label="Cloudflare Turnstile" description="Privacy-friendly, invisible challenge by Cloudflare. No user interaction required for most visitors." />
                    @else
                        <flux:radio value="turnstile" label="Cloudflare Turnstile" description="API keys not configured. Add them in Advanced Settings to enable this option." disabled />
                    @endif
                </flux:radio.group>

                @if (!$recaptchaConfigured || !$turnstileConfigured)
                    <flux:text class="mt-3 text-xs text-zinc-400">
                        <a href="{{ route('dashboard.settings.advanced') }}" wire:navigate class="text-primary underline hover:text-primary/80">Advanced Settings</a> → Spam Protection to add API keys.
                    </flux:text>
                @endif
            </div>

            <div>
                <flux:heading size="lg" class="mb-1">Form Fields</flux:heading>
                <flux:text class="mb-4 text-zinc-500">Configure which fields appear on the form and whether they are required.</flux:text>

                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Field</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Type</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Label</th>
                                <th class="text-center px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Show</th>
                                <th class="text-center px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Required</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($fields as $key => $config)
                                <tr wire:key="field-{{ $key }}" class="bg-white dark:bg-zinc-900">
                                    <td class="px-4 py-3 font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $key }}</td>
                                    <td class="px-4 py-3 hidden sm:table-cell">
                                        @php
                                            $ft = $config['field_type'] ?? 'text';
                                            $ftColor = match($ft) {
                                                'file'     => 'blue',
                                                'checkbox' => 'purple',
                                                'textarea' => 'lime',
                                                'email'    => 'yellow',
                                                'phone'    => 'orange',
                                                default    => 'zinc',
                                            };
                                        @endphp
                                        <flux:badge size="sm" color="{{ $ftColor }}">{{ ucfirst($ft) }}</flux:badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <flux:input wire:model="fields.{{ $key }}.label" type="text" size="sm" />
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <flux:switch wire:model.live="fields.{{ $key }}.enabled" />
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <flux:switch wire:model="fields.{{ $key }}.required" :disabled="!$fields[$key]['enabled']" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Form</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.forms.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

<?php

use App\Enums\FormType;
use App\Models\Form;
use App\Rules\CommaSeparatedEmails;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('New Form')] class extends Component {
    public string $type = 'contact';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate]
    public string $notificationEmail = '';

    public bool $saveSubmissions = true;

    /** @var array<string, array{enabled: bool, required: bool, label: string, field_type: string}> */
    public array $fields = [];

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'notificationEmail' => ['nullable', new CommaSeparatedEmails, 'max:500'],
        ];
    }

    public function mount(): void
    {
        $this->fields = FormType::Contact->defaultFields();
    }

    public function updatedType(): void
    {
        $this->fields = FormType::from($this->type)->defaultFields();
    }

    public function save(): void
    {
        $this->validate();

        Form::create([
            'name'               => $this->name,
            'type'               => $this->type,
            'notification_email' => $this->notificationEmail ?: null,
            'save_submissions'   => $this->saveSubmissions,
            'fields'             => $this->fields,
        ]);

        $this->redirect(route('dashboard.forms.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.forms.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">New Form</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-2xl space-y-6">
            <flux:field>
                <flux:label>Form Type</flux:label>
                <flux:select wire:model.live="type">
                    @foreach (\App\Enums\FormType::cases() as $case)
                        <flux:select.option value="{{ $case->value }}">{{ $case->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:description>Sets the default fields. You can still customize labels, visibility, and required status below.</flux:description>
            </flux:field>

            <flux:field>
                <flux:label>Form Name</flux:label>
                <flux:input wire:model="name" type="text" placeholder="e.g. Contact Form" autofocus required />
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
                    <span wire:loading.remove>Save Form</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.forms.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

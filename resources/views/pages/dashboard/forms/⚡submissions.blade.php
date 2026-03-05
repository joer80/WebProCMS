<?php

use App\Models\Form;
use App\Models\FormSubmission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Form Submissions')] #[Lazy] class extends Component {
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="flex items-center justify-center py-32">
            <svg class="animate-spin size-8 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        HTML;
    }

    public Form $form;

    public function mount(Form $form): void
    {
        $this->form = $form;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, FormSubmission> */
    public function getSubmissionsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return FormSubmission::query()
            ->where('form_id', $this->form->id)
            ->latest()
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.forms.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <div>
                <flux:heading size="xl">Submissions</flux:heading>
                <flux:text class="mt-1">{{ $this->form->name }}</flux:text>
            </div>
        </div>

        @if ($this->submissions->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="inbox" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No submissions yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($this->submissions as $submission)
                    <div wire:key="submission-{{ $submission->id }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $submission->created_at->format('M j, Y g:i A') }}
                            </span>
                            @if ($submission->ip_address)
                                <span class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">{{ $submission->ip_address }}</span>
                            @endif
                        </div>
                        <dl class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($submission->data as $key => $value)
                                @php
                                    $label    = $this->form->fields[$key]['label'] ?? ucwords(str_replace('_', ' ', $key));
                                    $fieldType = $this->form->fields[$key]['field_type'] ?? 'text';
                                @endphp
                                <div class="px-4 py-3 grid grid-cols-4 gap-4">
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</dt>
                                    <dd class="text-sm text-zinc-900 dark:text-zinc-100 col-span-3">
                                        @if ($fieldType === 'file' && $value)
                                            <a href="{{ Storage::url($value) }}" target="_blank" class="text-primary hover:underline inline-flex items-center gap-1">
                                                <x-heroicon name="arrow-down-tray" class="size-4" />
                                                Download
                                            </a>
                                        @elseif ($fieldType === 'checkbox')
                                            <span class="{{ $value ? 'text-green-600 dark:text-green-400' : 'text-zinc-400' }}">
                                                {{ $value ? 'Yes' : 'No' }}
                                            </span>
                                        @else
                                            {{ $value ?: '—' }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:main>
</div>

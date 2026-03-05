<?php

use App\Models\Form;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Forms')] #[Lazy] class extends Component {
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

    public ?int $confirmingDelete = null;

    public function deleteForm(int $formId): void
    {
        Form::query()->findOrFail($formId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Form> */
    public function getFormsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Form::query()
            ->withCount('submissions')
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Forms</flux:heading>
                <flux:text class="mt-1">Manage contact forms and view submissions.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.forms.create') }}" variant="primary" wire:navigate>
                New Form
            </flux:button>
        </div>

        @if ($this->forms->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="document-check" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No forms yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Type</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Notification Email</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Submissions</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Save to DB</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->forms as $form)
                            <tr wire:key="form-{{ $form->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $form->name }}
                                    @if ($form->is_seeded)
                                        <flux:badge size="sm" variant="outline" class="ml-2">Default</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <flux:badge size="sm" variant="outline">{{ $form->type->label() }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">
                                    {{ $form->notification_email ?: '—' }}
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    @if ($form->submissions_count > 0)
                                        <flux:button
                                            href="{{ route('dashboard.forms.submissions', $form) }}"
                                            variant="ghost"
                                            size="sm"
                                            wire:navigate
                                        >
                                            {{ $form->submissions_count }} {{ Str::plural('submission', $form->submissions_count) }}
                                        </flux:button>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-sm">None yet</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    @if ($form->save_submissions)
                                        <flux:badge color="green" size="sm">Yes</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">No</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            href="{{ route('dashboard.forms.edit', $form) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        @if ($confirmingDelete === $form->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteForm({{ $form->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', {{ $form->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="text-red-500 dark:text-red-400"
                                            />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:main>
</div>

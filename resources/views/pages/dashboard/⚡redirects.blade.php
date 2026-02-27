<?php

use App\Support\VoltFileService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Redirects')] #[Lazy] class extends Component {
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

    public bool $showModal = false;
    public ?string $editingFromPath = null;
    public ?string $confirmingDelete = null;

    public string $fromPath = '';
    public string $toUrl = '';
    public string $statusCode = '301';

    /** @return array<int, array{from: string, to: string, status: int}> */
    #[Computed]
    public function redirects(): array
    {
        return app(VoltFileService::class)->getRedirects();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(string $fromPath): void
    {
        $redirect = collect($this->redirects)->firstWhere('from', $fromPath);

        if (! $redirect) {
            return;
        }

        $this->editingFromPath = $fromPath;
        $this->fromPath = $redirect['from'];
        $this->toUrl = $redirect['to'];
        $this->statusCode = (string) $redirect['status'];
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'fromPath' => ['required', 'string', 'max:255'],
            'toUrl' => ['required', 'string', 'max:2048'],
            'statusCode' => ['required', 'in:301,302'],
        ]);

        $service = app(VoltFileService::class);

        if ($this->editingFromPath === null) {
            $service->createRedirect($validated['fromPath'], $validated['toUrl'], (int) $validated['statusCode']);
            $message = 'Redirect created.';
        } else {
            $service->updateRedirect(
                $this->editingFromPath,
                $validated['fromPath'],
                $validated['toUrl'],
                (int) $validated['statusCode']
            );
            $message = 'Redirect updated.';
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->redirects);

        $this->dispatch('notify', message: $message);
    }

    public function deleteRedirect(string $fromPath): void
    {
        app(VoltFileService::class)->removeRedirect($fromPath);
        $this->confirmingDelete = null;
        unset($this->redirects);

        $this->dispatch('notify', message: 'Redirect deleted.');
    }

    private function resetForm(): void
    {
        $this->editingFromPath = null;
        $this->fromPath = '';
        $this->toUrl = '';
        $this->statusCode = '301';
    }
}; ?>

<div>
    <flux:modal wire:model="showModal" class="max-w-md w-full">
        <flux:heading size="lg" class="mb-6">
            {{ $editingFromPath ? __('Edit Redirect') : __('New Redirect') }}
        </flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:input
                wire:model="fromPath"
                :label="__('From Path')"
                type="text"
                placeholder="old-page"
                required
                autofocus
                description="{{ __('The path visitors arrive at, e.g. old-page or /old-page.') }}"
            />

            <flux:input
                wire:model="toUrl"
                :label="__('To URL')"
                type="text"
                placeholder="/new-page"
                required
                description="{{ __('Destination path or full URL, e.g. /new-page or https://example.com.') }}"
            />

            <flux:select wire:model="statusCode" :label="__('Redirect Type')">
                <flux:select.option value="301">301 — Permanent</flux:select.option>
                <flux:select.option value="302">302 — Temporary</flux:select.option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingFromPath ? __('Update Redirect') : __('Create Redirect') }}</span>
                    <span wire:loading>{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">{{ __('Redirects') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Manage URL redirects for this site.') }}</flux:text>
            </div>
            <flux:button wire:click="openCreateModal" variant="primary">
                {{ __('New Redirect') }}
            </flux:button>
        </div>

        @if (empty($this->redirects))
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="arrow-right-circle" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">{{ __('No redirects configured.') }}</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">{{ __('From') }}</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">{{ __('To') }}</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">{{ __('Type') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->redirects as $redirect)
                            <tr wire:key="redirect-{{ $redirect['from'] }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    /{{ ltrim($redirect['from'], '/') }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">
                                    {{ $redirect['to'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <flux:badge color="{{ $redirect['status'] === 301 ? 'blue' : 'lime' }}" size="sm">
                                        {{ $redirect['status'] }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            wire:click="openEditModal('{{ $redirect['from'] }}')"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                        />
                                        @if ($confirmingDelete === $redirect['from'])
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteRedirect('{{ $redirect['from'] }}')" variant="danger" size="sm">
                                                    {{ __('Confirm') }}
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    {{ __('Cancel') }}
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', '{{ $redirect['from'] }}')"
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

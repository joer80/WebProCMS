<?php

use App\Support\VoltFileService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Pages')] class extends Component {
    /** @return array<string, array<string, string>> */
    #[Computed]
    public function voltFiles(): array
    {
        $files = (new VoltFileService)->listVoltFiles();
        unset($files['Dashboard']);

        return $files;
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Pages</flux:heading>
                <flux:text class="mt-1">Browse and edit your website pages.</flux:text>
            </div>
        </div>

        @forelse ($this->voltFiles as $section => $files)
            <div class="mb-8">
                <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs">
                    {{ $section }}
                </flux:heading>

                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($files as $label => $path)
                                <tr wire:key="page-{{ md5($path) }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $label }}</div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500 font-mono mt-0.5">{{ $path }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:button
                                            href="{{ route('dashboard.design-library.editor') . '?file=' . urlencode($path) . '&from=pages' }}"
                                            variant="outline"
                                            size="sm"
                                            icon="pencil-square"
                                        >
                                            Edit
                                        </flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-zinc-400 dark:text-zinc-500">
                <flux:icon name="document" class="size-12 mx-auto mb-3 opacity-40" />
                <p class="text-sm">No pages found.</p>
            </div>
        @endforelse
    </flux:main>
</div>

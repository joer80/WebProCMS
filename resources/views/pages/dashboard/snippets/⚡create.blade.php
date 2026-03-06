<?php

use App\Enums\SnippetPlacement;
use App\Enums\SnippetType;
use App\Models\Snippet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('New Snippet')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|in:html,js,php')]
    public string $type = 'html';

    #[Validate('required|in:head,body_end,php_top')]
    public string $placement = 'head';

    #[Validate('nullable|string')]
    public string $content = '';

    #[Validate('nullable|string|max:255')]
    public string $pagePath = '';

    #[Validate('boolean')]
    public bool $isActive = true;

    public function updatedType(string $value): void
    {
        $this->placement = SnippetType::from($value)->defaultPlacement()->value;
    }

    public function save(): void
    {
        $this->validate();

        Snippet::create([
            'name' => $this->name,
            'type' => $this->type,
            'placement' => $this->placement,
            'content' => $this->content,
            'page_path' => $this->pagePath ?: null,
            'is_active' => $this->isActive,
        ]);

        $this->redirect(route('dashboard.snippets.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.snippets.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">New Snippet</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-lg space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:description>A human-readable label for this snippet.</flux:description>
                <flux:input wire:model="name" type="text" placeholder="e.g. Google Analytics" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="type">
                    <flux:select.option value="html">HTML</flux:select.option>
                    <flux:select.option value="js">JavaScript</flux:select.option>
                    <flux:select.option value="php">PHP</flux:select.option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Placement</flux:label>
                <flux:description>Where on the page this snippet will be injected.</flux:description>
                <flux:select wire:model="placement" :disabled="$type === 'php'">
                    @if ($type === 'php')
                        <flux:select.option value="php_top">PHP (top of page)</flux:select.option>
                    @else
                        <flux:select.option value="head">Head (before &lt;/head&gt;)</flux:select.option>
                        <flux:select.option value="body_end">Scripts (before &lt;/body&gt;)</flux:select.option>
                    @endif
                </flux:select>
                <flux:error name="placement" />
            </flux:field>

            <flux:field>
                <flux:label>Content</flux:label>
                @if ($type === 'html')
                    <flux:description>HTML to inject at the chosen placement. Can include <code class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">&lt;script&gt;</code>, <code class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">&lt;link&gt;</code>, comments, or any HTML.</flux:description>
                @elseif ($type === 'js')
                    <flux:description>JavaScript to inject. You can include bare JS or wrap it in <code class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">&lt;script&gt;</code> tags.</flux:description>
                @else
                    <flux:description>PHP code evaluated at the top of the page before any output. Do not include opening/closing PHP tags.</flux:description>
                @endif
                <flux:textarea wire:model="content" rows="10" class="font-mono text-sm" placeholder="Paste your code here…" />
                <flux:error name="content" />
            </flux:field>

            <flux:field>
                <flux:label>Page Path <flux:badge variant="zinc" size="sm">Optional</flux:badge></flux:label>
                <flux:description>Leave blank to apply to all public pages. Enter a path (e.g. <code class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">/thank-you</code>) to limit to one page.</flux:description>
                <flux:input wire:model="pagePath" type="text" placeholder="/thank-you" />
                <flux:error name="pagePath" />
            </flux:field>

            <flux:switch wire:model="isActive" label="Active" description="Inactive snippets are not injected into pages." />

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Snippet</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.snippets.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

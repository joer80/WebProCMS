<?php

use App\Enums\SnippetPlacement;
use App\Enums\SnippetType;
use App\Models\Snippet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Edit Snippet')] class extends Component {
    public Snippet $snippet;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $type = 'html';

    #[Validate]
    public string $placement = 'head';

    #[Validate]
    public string $content = '';

    #[Validate]
    public string $pagePath = '';

    #[Validate]
    public bool $isActive = true;

    public function mount(Snippet $snippet): void
    {
        $this->snippet = $snippet;
        $this->name = $snippet->name;
        $this->type = $snippet->type->value;
        $this->placement = $snippet->placement->value;
        $this->content = $snippet->content;
        $this->pagePath = $snippet->page_path ?? '';
        $this->isActive = $snippet->is_active;
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:html,js,php'],
            'placement' => ['required', 'in:head,body_end,php_top'],
            'content' => ['nullable', 'string'],
            'pagePath' => ['nullable', 'string', 'max:255'],
            'isActive' => ['boolean'],
        ];
    }

    public function updatedType(string $value): void
    {
        $this->placement = SnippetType::from($value)->defaultPlacement()->value;
    }

    public function save(): void
    {
        $this->validate();

        $this->snippet->update([
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
            <flux:heading size="xl">Edit Snippet</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-lg space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:description>A human-readable label for this snippet.</flux:description>
                <flux:input wire:model="name" type="text" autofocus required />
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
                <flux:textarea wire:model="content" rows="10" class="font-mono text-sm" />
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
                    <span wire:loading.remove>Update Snippet</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.snippets.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

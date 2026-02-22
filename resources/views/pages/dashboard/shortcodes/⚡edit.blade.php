<?php

use App\Models\Shortcode;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] #[Title('Edit Shortcode')] class extends Component {
    public Shortcode $shortcode;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $tag = '';

    #[Validate]
    public string $type = 'single_text';

    #[Validate]
    public string $content = '';

    #[Validate]
    public bool $isActive = true;

    public function mount(Shortcode $shortcode): void
    {
        $this->shortcode = $shortcode;
        $this->name = $shortcode->name;
        $this->tag = $shortcode->tag;
        $this->type = $shortcode->type;
        $this->content = $shortcode->content ?? '';
        $this->isActive = $shortcode->is_active;
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tag' => ['required', 'string', 'max:100', 'regex:/^[\w-]+$/', Rule::unique('shortcodes', 'tag')->ignore($this->shortcode->id)],
            'type' => ['required', Rule::in(['single_text', 'rich_text', 'php_code'])],
            'content' => ['nullable', 'string'],
            'isActive' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->shortcode->update([
            'name' => $this->name,
            'tag' => $this->tag,
            'type' => $this->type,
            'content' => $this->content ?: null,
            'is_active' => $this->isActive,
        ]);

        $this->redirect(route('dashboard.shortcodes.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.shortcodes.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Shortcode</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-lg space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:description>A human-readable label for this shortcode.</flux:description>
                <flux:input wire:model="name" type="text" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Tag</flux:label>
                <flux:description>Used as <code class="font-mono text-xs">[[{{ $shortcode->tag }}]]</code> in your content. Changing this will break existing usages.</flux:description>
                <flux:input wire:model="tag" type="text" required />
                <flux:error name="tag" />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="type">
                    <flux:select.option value="single_text">Single Text/HTML</flux:select.option>
                    <flux:select.option value="rich_text">Rich Text/HTML</flux:select.option>
                    <flux:select.option value="php_code">PHP Code</flux:select.option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Content</flux:label>
                @if ($type === 'single_text')
                    <flux:description>Plain text or simple HTML that will replace the shortcode tag.</flux:description>
                    <flux:input wire:model="content" type="text" />
                @elseif ($type === 'rich_text')
                    <flux:description>HTML content that will be injected unescaped when the shortcode is replaced.</flux:description>
                    <flux:textarea wire:model="content" rows="8" class="font-mono text-sm" />
                @else
                    <flux:description>PHP code that will be evaluated. Output is captured and inserted. Do not include opening/closing PHP tags.</flux:description>
                    <flux:textarea wire:model="content" rows="8" class="font-mono text-sm" />
                @endif
                <flux:error name="content" />
            </flux:field>

            <flux:switch wire:model="isActive" label="Active" description="Inactive shortcodes are ignored during content processing." />

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Shortcode</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.shortcodes.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

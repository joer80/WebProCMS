<?php

use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Edit Content Type')] class extends Component {
    public int $contentTypeId = 0;

    public string $name = '';

    public string $slug = '';

    public string $singular = '';

    public string $icon = 'document';

    /** @var array<int, array{label: string, name: string, type: string, options: string, required: bool}> */
    public array $fields = [];

    public function mount(int $contentTypeId): void
    {
        $type = ContentTypeDefinition::query()->findOrFail($contentTypeId);

        $this->contentTypeId = $contentTypeId;
        $this->name = $type->name;
        $this->slug = $type->slug;
        $this->singular = $type->singular;
        $this->icon = $type->icon;
        $this->fields = array_map(function (array $field): array {
            return [
                'label' => $field['label'] ?? '',
                'name' => $field['name'] ?? '',
                'type' => $field['type'] ?? 'text',
                'options' => $field['options'] ?? '',
                'required' => (bool) ($field['required'] ?? false),
            ];
        }, $type->fields ?? []);
    }

    public function addField(): void
    {
        $this->fields[] = [
            'label' => '',
            'name' => '',
            'type' => 'text',
            'options' => '',
            'required' => false,
        ];
    }

    public function removeField(int $index): void
    {
        array_splice($this->fields, $index, 1);
        $this->fields = array_values($this->fields);
    }

    public function moveFieldUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        [$this->fields[$index - 1], $this->fields[$index]] = [$this->fields[$index], $this->fields[$index - 1]];
    }

    public function moveFieldDown(int $index): void
    {
        if ($index >= count($this->fields) - 1) {
            return;
        }

        [$this->fields[$index + 1], $this->fields[$index]] = [$this->fields[$index], $this->fields[$index + 1]];
    }

    public function updatedFieldsLabel(string $value, string $key): void
    {
        $parts = explode('.', $key);
        $idx = (int) $parts[0];

        if (isset($this->fields[$idx])) {
            $this->fields[$idx]['name'] = Str::snake($value);
        }
    }

    public function save(): void
    {
        $type = ContentTypeDefinition::query()->findOrFail($this->contentTypeId);

        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:content_type_definitions,slug,'.$type->id,
            'singular' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.name' => 'required|string|max:255',
        ]);

        $type->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'singular' => $this->singular,
            'icon' => $this->icon,
            'fields' => $this->fields,
        ]);

        $this->dispatch('notify', message: 'Content type updated.');
    }

    public function delete(): void
    {
        $type = ContentTypeDefinition::query()->findOrFail($this->contentTypeId);

        ContentItem::query()->where('type_slug', $type->slug)->delete();
        $type->delete();

        $this->redirect(route('dashboard.content-types.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('dashboard.content-types.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
                <flux:heading size="xl">Edit Content Type</flux:heading>
            </div>
            <flux:button
                wire:click="delete"
                wire:confirm="Delete '{{ $name }}' and all its content? This cannot be undone."
                variant="danger"
                size="sm"
                icon="trash"
            >
                Delete Type
            </flux:button>
        </div>

        <form wire:submit="save">
            <div class="grid lg:grid-cols-[1fr_288px] gap-8 items-start">

                {{-- Left: main content --}}
                <div class="space-y-6">

                    {{-- Type Details --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-6 space-y-4">
                        <flux:heading size="sm">Type Details</flux:heading>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Name <flux:badge size="sm" variant="outline" class="ml-1">Plural</flux:badge></flux:label>
                                <flux:input wire:model="name" type="text" placeholder="Meeting Notes" required />
                                <flux:description>The plural display name, e.g. "Meeting Notes".</flux:description>
                                <flux:error name="name" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Singular Name</flux:label>
                                <flux:input wire:model="singular" type="text" placeholder="Meeting Note" required />
                                <flux:description>The singular display name, e.g. "Meeting Note".</flux:description>
                                <flux:error name="singular" />
                            </flux:field>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Slug</flux:label>
                                <flux:input wire:model="slug" type="text" placeholder="meeting-notes" required />
                                <flux:description>URL-safe identifier. Changing this will break existing content URLs.</flux:description>
                                <flux:error name="slug" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Icon</flux:label>
                                <flux:input wire:model="icon" type="text" placeholder="document" />
                                <flux:description>Heroicon name (e.g. document, briefcase, star).</flux:description>
                                <flux:error name="icon" />
                            </flux:field>
                        </div>
                    </div>

                    {{-- Field Builder --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:heading size="sm">Fields</flux:heading>
                            <flux:button type="button" wire:click="addField" size="sm" variant="ghost" icon="plus">
                                Add Field
                            </flux:button>
                        </div>

                        @if (count($fields) === 0)
                            <p class="text-sm text-center text-zinc-400 dark:text-zinc-500 py-4">
                                No fields yet. Click "Add Field" to define the structure of this content type.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach ($fields as $index => $field)
                                    <div wire:key="field-{{ $index }}" class="rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Field {{ $index + 1 }}</span>
                                            <div class="flex items-center gap-1">
                                                <flux:button
                                                    type="button"
                                                    wire:click="moveFieldUp({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="chevron-up"
                                                    :disabled="$index === 0"
                                                />
                                                <flux:button
                                                    type="button"
                                                    wire:click="moveFieldDown({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="chevron-down"
                                                    :disabled="$index === count($fields) - 1"
                                                />
                                                <flux:button
                                                    type="button"
                                                    wire:click="removeField({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="text-red-500 dark:text-red-400"
                                                />
                                            </div>
                                        </div>

                                        <div class="grid sm:grid-cols-3 gap-3">
                                            <flux:field>
                                                <flux:label>Label</flux:label>
                                                <flux:input wire:model.live="fields.{{ $index }}.label" type="text" placeholder="Field Label" />
                                                <flux:error name="fields.{{ $index }}.label" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>Name (key)</flux:label>
                                                <flux:input wire:model="fields.{{ $index }}.name" type="text" placeholder="field_name" />
                                                <flux:error name="fields.{{ $index }}.name" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>Type</flux:label>
                                                <flux:select wire:model.live="fields.{{ $index }}.type">
                                                    <flux:select.option value="text">Text</flux:select.option>
                                                    <flux:select.option value="richtext">Rich Text (textarea)</flux:select.option>
                                                    <flux:select.option value="richtext_tiptap">Rich Text (Tiptap editor)</flux:select.option>
                                                    <flux:select.option value="date">Date</flux:select.option>
                                                    <flux:select.option value="select">Select (dropdown)</flux:select.option>
                                                    <flux:select.option value="image">Image</flux:select.option>
                                                    <flux:select.option value="gallery">Gallery</flux:select.option>
                                                    <flux:select.option value="toggle">Toggle (on/off)</flux:select.option>
                                                    <flux:select.option value="radio">Radio Buttons</flux:select.option>
                                                    <flux:select.option value="checkbox">Checkbox (single)</flux:select.option>
                                                    <flux:select.option value="checkboxes">Checkboxes (multiple)</flux:select.option>
                                                    <flux:select.option value="oembed">oEmbed URL</flux:select.option>
                                                    <flux:select.option value="file">File Upload (single)</flux:select.option>
                                                    <flux:select.option value="files">File Upload (multiple)</flux:select.option>
                                                </flux:select>
                                                <flux:error name="fields.{{ $index }}.type" />
                                            </flux:field>
                                        </div>

                                        @if (in_array($field['type'] ?? 'text', ['select', 'radio', 'checkboxes']))
                                            <flux:field>
                                                <flux:label>Options</flux:label>
                                                <flux:input wire:model="fields.{{ $index }}.options" type="text" placeholder="Option 1, Option 2, Option 3" />
                                                <flux:description>Comma-separated list of options.</flux:description>
                                            </flux:field>
                                        @endif

                                        <flux:switch wire:model="fields.{{ $index }}.required" label="Required field" />
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <flux:button type="button" wire:click="addField" variant="ghost" icon="plus" class="w-full">
                            Add Field
                        </flux:button>
                    </div>
                </div>

                {{-- Right: sidebar --}}
                <div class="space-y-4 lg:sticky lg:top-4">
                    <flux:button type="submit" variant="primary" class="w-full justify-center" wire:loading.attr="disabled">
                        Save Changes
                    </flux:button>
                    <flux:button href="{{ route('dashboard.content-types.index') }}" variant="ghost" class="w-full justify-center" wire:navigate>
                        Cancel
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:main>
</div>

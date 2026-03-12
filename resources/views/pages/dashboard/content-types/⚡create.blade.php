<?php

use App\Models\ContentTypeDefinition;
use App\Support\ContentTypePageGenerator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('New Content Type')] class extends Component {
    public string $name = '';

    public string $slug = '';

    public string $singular = '';

    public string $icon = 'document';

    /** @var array<int, array{label: string, name: string, type: string, options: string, required: bool}> */
    public array $fields = [];

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
        $this->singular = $value;
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
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:content_type_definitions,slug',
            'singular' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.name' => 'required|string|max:255',
        ]);

        $typeDef = ContentTypeDefinition::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'singular' => $this->singular,
            'icon' => $this->icon,
            'fields' => $this->fields,
            'sort_order' => 0,
        ]);

        (new ContentTypePageGenerator)->generate($typeDef);

        $this->redirect(route('dashboard.content-types.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.content-types.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">New Content Type</flux:heading>
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
                                <flux:input wire:model.live="name" type="text" placeholder="Meeting Notes" autofocus required />
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
                                <flux:description>URL-safe identifier. Auto-generated from name.</flux:description>
                                <flux:error name="slug" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Icon</flux:label>
                                @php
                                    $allIconsData = require resource_path('heroicons/data.php');
                                    $outlineIcons = array_keys($allIconsData['outline']);
                                    $solidIcons   = array_keys($allIconsData['solid']);
                                @endphp
                                <div x-data="{ pickerOpen: false, search: '', variant: 'outline', icon: '{{ $icon }}' }">
                                    <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div wire:key="icon-preview-{{ $icon }}" class="size-5 shrink-0 text-zinc-600 dark:text-zinc-300">
                                            <x-heroicon :name="str_contains($icon, ':') ? substr($icon, 0, strpos($icon, ':')) : $icon" :variant="str_contains($icon, ':solid') ? 'solid' : 'outline'" class="size-5" />
                                        </div>
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400 flex-1 font-mono truncate" x-text="icon || '—'"></span>
                                        <button type="button" @click="pickerOpen = true" class="text-xs text-primary hover:text-primary/80 shrink-0 transition-colors">Change</button>
                                    </div>
                                    <div
                                        x-show="pickerOpen"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-6"
                                    >
                                        <div class="absolute inset-0 bg-black/50" @click="pickerOpen = false; search = ''"></div>
                                        <div class="relative z-10 bg-white dark:bg-zinc-800 rounded-xl shadow-2xl flex flex-col w-full max-w-2xl max-h-[80vh]">
                                            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">Select Icon</p>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 p-0.5">
                                                        <button type="button" @click="variant = 'outline'" x-bind:class="variant === 'outline' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Outline</button>
                                                        <button type="button" @click="variant = 'solid'" x-bind:class="variant === 'solid' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Solid</button>
                                                    </div>
                                                    <button type="button" @click="pickerOpen = false; search = ''" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                                                        <flux:icon name="x-mark" class="size-4" />
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="px-5 py-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                                                <input
                                                    x-model="search"
                                                    type="text"
                                                    placeholder="Search icons… or press Enter to use any name"
                                                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                    @keydown.enter.prevent="if (search.trim()) { icon = search.trim(); $wire.set('icon', search.trim()); search = ''; pickerOpen = false; }"
                                                />
                                            </div>
                                            <div class="overflow-y-auto p-5">
                                                <div x-show="variant === 'outline'" class="grid grid-cols-10 gap-1">
                                                    @foreach ($outlineIcons as $iconName)
                                                        <button type="button" x-show="!search || '{{ $iconName }}'.includes(search)" @click="icon = '{{ $iconName }}'; $wire.set('icon', '{{ $iconName }}'); search = ''; pickerOpen = false" x-bind:class="icon === '{{ $iconName }}' ? 'border-primary bg-primary/10 text-primary' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400'" class="flex items-center justify-center p-2 rounded-lg border transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" class="size-5" /></button>
                                                    @endforeach
                                                </div>
                                                <div x-show="variant === 'solid'" class="grid grid-cols-10 gap-1">
                                                    @foreach ($solidIcons as $iconName)
                                                        <button type="button" x-show="!search || '{{ $iconName }}'.includes(search)" @click="icon = '{{ $iconName }}:solid'; $wire.set('icon', '{{ $iconName }}:solid'); search = ''; pickerOpen = false" x-bind:class="icon === '{{ $iconName }}:solid' ? 'border-primary bg-primary/10 text-primary' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400'" class="flex items-center justify-center p-2 rounded-lg border transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" variant="solid" class="size-5" /></button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <flux:description>Search or browse all Heroicons. Outline and solid variants available.</flux:description>
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
                        Create Content Type
                    </flux:button>
                    <flux:button href="{{ route('dashboard.content-types.index') }}" variant="ghost" class="w-full justify-center" wire:navigate>
                        Cancel
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:main>
</div>

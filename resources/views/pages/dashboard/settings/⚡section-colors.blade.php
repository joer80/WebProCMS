<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Section Colors')] class extends Component {
    /** @var list<array{id: string, label: string, bg_classes: string, text_classes: string, bg_image: string, bg_position: string, bg_size: string, bg_repeat: string}> */
    public array $presets = [];

    public bool $showForm = false;

    public ?string $editingId = null;

    public string $formLabel = '';

    public string $formBgClasses = '';

    public string $formTextClasses = '';

    public string $formBgImage = '';

    public string $formBgPosition = '';

    public string $formBgSize = '';

    public string $formBgRepeat = '';

    public ?string $confirmingDeleteId = null;

    public bool $showMediaPicker = false;

    public function mount(): void
    {
        $saved = Setting::get('section_style_presets', null);

        if ($saved === null) {
            $this->presets = $this->defaultPresets();
            Setting::set('section_style_presets', $this->presets);
        } else {
            $this->presets = (array) $saved;
        }
    }

    public function openAdd(): void
    {
        $this->editingId = null;
        $this->formLabel = '';
        $this->formBgClasses = '';
        $this->formTextClasses = '';
        $this->formBgImage = '';
        $this->formBgPosition = '';
        $this->formBgSize = '';
        $this->formBgRepeat = '';
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $preset = collect($this->presets)->firstWhere('id', $id);

        if (! $preset) {
            return;
        }

        $this->editingId = $id;
        $this->formLabel = $preset['label'] ?? '';
        $this->formBgClasses = $preset['bg_classes'] ?? '';
        $this->formTextClasses = $preset['text_classes'] ?? '';
        $this->formBgImage = $preset['bg_image'] ?? '';
        $this->formBgPosition = $preset['bg_position'] ?? '';
        $this->formBgSize = $preset['bg_size'] ?? '';
        $this->formBgRepeat = $preset['bg_repeat'] ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formLabel' => ['required', 'string', 'max:100'],
        ]);

        if ($this->editingId) {
            $this->presets = array_map(function (array $preset): array {
                if ($preset['id'] === $this->editingId) {
                    return array_merge($preset, [
                        'label'       => $this->formLabel,
                        'bg_classes'  => $this->formBgClasses,
                        'text_classes' => $this->formTextClasses,
                        'bg_image'    => $this->formBgImage,
                        'bg_position' => $this->formBgPosition,
                        'bg_size'     => $this->formBgSize,
                        'bg_repeat'   => $this->formBgRepeat,
                    ]);
                }

                return $preset;
            }, $this->presets);
        } else {
            $id = Str::slug($this->formLabel);
            $base = $id;
            $i = 2;
            $existingIds = array_column($this->presets, 'id');

            while (in_array($id, $existingIds, true)) {
                $id = $base.'-'.$i++;
            }

            $this->presets[] = [
                'id'          => $id,
                'label'       => $this->formLabel,
                'bg_classes'  => $this->formBgClasses,
                'text_classes' => $this->formTextClasses,
                'bg_image'    => $this->formBgImage,
                'bg_position' => $this->formBgPosition,
                'bg_size'     => $this->formBgSize,
                'bg_repeat'   => $this->formBgRepeat,
            ];
        }

        Setting::set('section_style_presets', $this->presets);
        $this->showForm = false;
        $this->dispatch('notify', message: 'Section style saved.');
    }

    public function cancel(): void
    {
        $this->showForm = false;
    }

    public function confirmDelete(string $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function deletePreset(): void
    {
        $this->presets = array_values(array_filter(
            $this->presets,
            fn (array $p) => $p['id'] !== $this->confirmingDeleteId
        ));

        Setting::set('section_style_presets', $this->presets);
        $this->confirmingDeleteId = null;
        $this->dispatch('notify', message: 'Section style deleted.');
    }

    public function openMediaPicker(): void
    {
        $this->showMediaPicker = true;
    }

    public function removeImage(): void
    {
        $this->formBgImage = '';
    }

    #[On('media-image-picked')]
    public function handleMediaImagePicked(string $key, string $path, string $alt = ''): void
    {
        if ($key !== 'section-style-bg') {
            return;
        }

        $this->formBgImage = $path;
        $this->showMediaPicker = false;
    }

    /**
     * @return list<array{id: string, label: string, bg_classes: string, text_classes: string, bg_image: string, bg_position: string, bg_size: string, bg_repeat: string}>
     */
    private function defaultPresets(): array
    {
        return [
            ['id' => 'light', 'label' => 'Light', 'bg_classes' => 'bg-white', 'text_classes' => 'text-zinc-900', 'bg_image' => '', 'bg_position' => '', 'bg_size' => '', 'bg_repeat' => ''],
            ['id' => 'light-alt', 'label' => 'Light Alt', 'bg_classes' => 'bg-zinc-50', 'text_classes' => 'text-zinc-900', 'bg_image' => '', 'bg_position' => '', 'bg_size' => '', 'bg_repeat' => ''],
            ['id' => 'dark', 'label' => 'Dark', 'bg_classes' => 'dark bg-zinc-900', 'text_classes' => 'text-white', 'bg_image' => '', 'bg_position' => '', 'bg_size' => '', 'bg_repeat' => ''],
            ['id' => 'dark-alt', 'label' => 'Dark Alt', 'bg_classes' => 'dark bg-zinc-800', 'text_classes' => 'text-white', 'bg_image' => '', 'bg_position' => '', 'bg_size' => '', 'bg_repeat' => ''],
            ['id' => 'primary', 'label' => 'Primary', 'bg_classes' => 'bg-primary', 'text_classes' => 'text-white', 'bg_image' => '', 'bg_position' => '', 'bg_size' => '', 'bg_repeat' => ''],
        ];
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <flux:heading size="xl">Section Colors</flux:heading>
                <flux:text class="mt-1">Named colour and background presets for page sections. Editors apply these from the Section Design panel in the page editor.</flux:text>
            </div>
            @if (! $showForm)
                <flux:button variant="primary" icon="plus" wire:click="openAdd">Add Style</flux:button>
            @endif
        </div>

        <div class="max-w-2xl space-y-4">

            {{-- Add / Edit form --}}
            @if ($showForm)
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading class="mb-4">{{ $editingId ? 'Edit Section Style' : 'New Section Style' }}</flux:heading>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Name</flux:label>
                            <flux:input wire:model="formLabel" placeholder="e.g. Dark, Primary, Hero Photo" />
                            <flux:error name="formLabel" />
                        </flux:field>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Background Classes</flux:label>
                                <flux:input wire:model="formBgClasses" placeholder="bg-zinc-900 dark" class="font-mono" />
                                <flux:description>Tailwind bg-* classes. Include <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">dark</code> to force dark mode variants.</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>Text Classes</flux:label>
                                <flux:input wire:model="formTextClasses" placeholder="text-white" class="font-mono" />
                                <flux:description>Tailwind text-* colour class.</flux:description>
                            </flux:field>
                        </div>

                        {{-- Background image --}}
                        <flux:field>
                            <flux:label>Background Image</flux:label>
                            @if ($formBgImage)
                                <div class="mt-1 mb-2 rounded-md overflow-hidden border border-zinc-200 dark:border-zinc-700 w-full h-24 bg-cover bg-center relative" style="background-image: url('{{ Storage::url($formBgImage) }}')">
                                    <button type="button" wire:click="removeImage" class="absolute top-1 right-1 bg-white dark:bg-zinc-900 rounded-full p-0.5 shadow text-zinc-400 hover:text-red-500 transition-colors">
                                        <flux:icon name="x-mark" class="size-4" />
                                    </button>
                                </div>
                            @endif
                            <flux:button variant="outline" icon="photo" wire:click="openMediaPicker" size="sm">
                                {{ $formBgImage ? 'Change image' : 'Pick from Media Library' }}
                            </flux:button>
                        </flux:field>

                        <div class="grid grid-cols-3 gap-4">
                                <flux:field>
                                    <flux:label>Position</flux:label>
                                    <flux:select wire:model="formBgPosition">
                                        <option value="">— default —</option>
                                        <option value="center">Center</option>
                                        <option value="top">Top</option>
                                        <option value="bottom">Bottom</option>
                                        <option value="left">Left</option>
                                        <option value="right">Right</option>
                                        <option value="left-top">Top Left</option>
                                        <option value="left-bottom">Bottom Left</option>
                                        <option value="right-top">Top Right</option>
                                        <option value="right-bottom">Bottom Right</option>
                                    </flux:select>
                                </flux:field>

                                <flux:field>
                                    <flux:label>Size</flux:label>
                                    <flux:select wire:model="formBgSize">
                                        <option value="">— default —</option>
                                        <option value="cover">Cover</option>
                                        <option value="contain">Contain</option>
                                        <option value="auto">Auto</option>
                                    </flux:select>
                                </flux:field>

                                <flux:field>
                                    <flux:label>Repeat</flux:label>
                                    <flux:select wire:model="formBgRepeat">
                                        <option value="">— default —</option>
                                        <option value="no-repeat">No Repeat</option>
                                        <option value="repeat">Repeat (tile)</option>
                                        <option value="repeat-x">Repeat X</option>
                                        <option value="repeat-y">Repeat Y</option>
                                    </flux:select>
                                </flux:field>
                            </div>

                        <div class="flex items-center gap-2 pt-2">
                            <flux:button variant="primary" wire:click="save">Save</flux:button>
                            <flux:button variant="ghost" wire:click="cancel">Cancel</flux:button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Preset list --}}
            @forelse ($presets as $preset)
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center gap-4">
                        {{-- Colour preview swatches --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            @php
                                $bgHint = str_contains($preset['bg_classes'] ?? '', 'zinc-900') || str_contains($preset['bg_classes'] ?? '', 'zinc-800')
                                    ? 'bg-zinc-900' : (str_contains($preset['bg_classes'] ?? '', 'zinc-50') ? 'bg-zinc-50' : (str_contains($preset['bg_classes'] ?? '', 'primary') ? 'bg-primary' : 'bg-white'));
                                $textHint = str_contains($preset['text_classes'] ?? '', 'white') ? 'text-white' : 'text-zinc-900';
                            @endphp
                            <div class="size-7 rounded border border-zinc-300 dark:border-zinc-600 flex items-center justify-center {{ $bgHint }}">
                                <span class="text-[10px] font-bold leading-none {{ $textHint }}">Aa</span>
                            </div>
                            @if (! empty($preset['bg_image']))
                                <flux:icon name="photo" class="size-4 text-zinc-400" title="Has background image" />
                            @endif
                        </div>

                        {{-- Label + classes --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $preset['label'] }}</div>
                            <div class="text-xs text-zinc-400 font-mono truncate">{{ trim(($preset['bg_classes'] ?? '') . ' ' . ($preset['text_classes'] ?? '')) ?: '—' }}</div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($confirmingDeleteId === $preset['id'])
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Delete?</span>
                                <flux:button size="sm" variant="danger" wire:click="deletePreset">Yes, delete</flux:button>
                                <flux:button size="sm" variant="ghost" wire:click="$set('confirmingDeleteId', null)">Cancel</flux:button>
                            @else
                                <flux:button size="sm" variant="ghost" icon="pencil" wire:click="openEdit('{{ $preset['id'] }}')">Edit</flux:button>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete('{{ $preset['id'] }}')">Delete</flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 text-center">
                    <flux:text class="text-zinc-400">No section styles yet. Add one to get started.</flux:text>
                </div>
            @endforelse
        </div>
    </flux:main>

    @if ($showMediaPicker)
        <livewire:pages::dashboard.media-library.picker field-key="section-style-bg" :key="'section-style-picker'" />
    @endif
</div>

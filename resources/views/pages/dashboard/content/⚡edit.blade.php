<?php

use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Edit Item')] class extends Component {
    use WithFileUploads;

    public string $typeSlug = '';

    public int $itemId = 0;

    public ?ContentTypeDefinition $typeDef = null;

    public ?ContentItem $item = null;

    public string $status = 'draft';

    /** @var array<string, mixed> */
    public array $formData = [];

    /** @var array<string, mixed> Temporary Livewire upload properties for image fields */
    public array $imageUploads = [];

    /** @var array<string, array<int, array{path: string, alt: string}>> Gallery state keyed by field name */
    public array $galleryData = [];

    /** @var mixed Temporary upload for gallery add */
    public $newGalleryUpload = null;

    public function mount(string $typeSlug, int $itemId): void
    {
        $this->typeSlug = $typeSlug;
        $this->itemId = $itemId;
        $this->typeDef = ContentTypeDefinition::where('slug', $typeSlug)->firstOrFail();
        $this->item = ContentItem::query()->findOrFail($itemId);

        if ($this->item->type_slug !== $typeSlug) {
            abort(404);
        }

        $this->status = $this->item->status;

        $savedData = $this->item->data ?? [];

        foreach ($this->typeDef->fields as $field) {
            $type = $field['type'] ?? 'text';
            $name = $field['name'] ?? '';

            $default = match ($type) {
                'toggle', 'checkbox' => false,
                'checkboxes' => [],
                'gallery' => [],
                default => '',
            };

            $this->formData[$name] = $savedData[$name] ?? $default;

            if ($type === 'gallery') {
                $this->galleryData[$name] = is_array($savedData[$name] ?? null) ? $savedData[$name] : [];
            }
        }
    }

    public function uploadImage(string $fieldName): void
    {
        $upload = $this->imageUploads[$fieldName] ?? null;

        if (! $upload) {
            return;
        }

        $this->validate(["imageUploads.{$fieldName}" => 'nullable|image|max:51200']);

        $oldPath = $this->formData[$fieldName] ?? null;

        if ($oldPath && is_string($oldPath) && $oldPath !== '') {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $upload->store('content', 'public');
        ImageResizer::resizeToMaxWidth($path);

        $this->formData[$fieldName] = $path;
        $this->imageUploads[$fieldName] = null;
    }

    public function removeImage(string $fieldName): void
    {
        $path = $this->formData[$fieldName] ?? null;

        if ($path && is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }

        $this->formData[$fieldName] = '';
    }

    public function addGalleryImage(string $fieldName): void
    {
        $this->validate(['newGalleryUpload' => 'nullable|image|max:51200']);

        if (! $this->newGalleryUpload) {
            return;
        }

        $path = $this->newGalleryUpload->store('content', 'public');
        ImageResizer::resizeToMaxWidth($path);

        $this->galleryData[$fieldName][] = ['path' => $path, 'alt' => ''];
        $this->formData[$fieldName] = $this->galleryData[$fieldName];
        $this->reset('newGalleryUpload');
    }

    public function removeGalleryImage(string $fieldName, int $index): void
    {
        $path = $this->galleryData[$fieldName][$index]['path'] ?? null;

        if ($path) {
            Storage::disk('public')->delete($path);
            array_splice($this->galleryData[$fieldName], $index, 1);
            $this->galleryData[$fieldName] = array_values($this->galleryData[$fieldName]);
            $this->formData[$fieldName] = $this->galleryData[$fieldName];
        }
    }

    private function performSave(): void
    {
        $rules = [
            'status' => 'required|in:draft,published',
        ];

        foreach ($this->typeDef->fields as $field) {
            $name = $field['name'] ?? '';
            $required = $field['required'] ?? false;
            $type = $field['type'] ?? 'text';

            if (in_array($type, ['image', 'gallery'])) {
                continue;
            }

            $rule = $required ? 'required' : 'nullable';

            $rules["formData.{$name}"] = $rule . match ($type) {
                'toggle', 'checkbox' => '|boolean',
                'checkboxes' => '|array',
                default => '|string',
            };
        }

        $this->validate($rules);

        $wasPublished = $this->item->status === 'published';
        $isPublishing = $this->status === 'published';

        $this->item->update([
            'data' => $this->formData,
            'status' => $this->status,
            'published_at' => $isPublishing && ! $wasPublished ? now() : $this->item->published_at,
        ]);
    }

    public function save(): void
    {
        $this->performSave();
        $this->dispatch('notify', message: 'Item saved.');
    }

    public function saveAndExit(): void
    {
        $this->performSave();
        $this->redirect(route('dashboard.content.index', $this->typeSlug), navigate: true);
    }

    public function delete(): void
    {
        $this->item->delete();
        $this->redirect(route('dashboard.content.index', $this->typeSlug), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('dashboard.content.index', $typeSlug) }}" variant="ghost" icon="arrow-left" wire:navigate />
                <flux:heading size="xl">Edit {{ $typeDef->singular }}</flux:heading>
            </div>
            <flux:button
                wire:click="delete"
                wire:confirm="Delete this item? This cannot be undone."
                variant="danger"
                size="sm"
                icon="trash"
            >
                Delete
            </flux:button>
        </div>

        <form wire:submit="save">
            <div class="grid lg:grid-cols-[1fr_288px] gap-8 items-start">

                {{-- Left: dynamic fields --}}
                <div class="space-y-6">
                    @forelse ($typeDef->fields as $field)
                        @php
                            $fieldName = $field['name'] ?? '';
                            $fieldLabel = $field['label'] ?? '';
                            $fieldType = $field['type'] ?? 'text';
                            $fieldRequired = $field['required'] ?? false;
                            $fieldOptions = array_map('trim', explode(',', $field['options'] ?? ''));
                        @endphp

                        @if ($fieldType === 'text')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <flux:input wire:model="formData.{{ $fieldName }}" type="text" :required="$fieldRequired" />
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'richtext')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <flux:textarea wire:model="formData.{{ $fieldName }}" rows="8" :required="$fieldRequired" />
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'richtext_tiptap')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <div
                                    wire:ignore
                                    x-data="richEditor(@js($formData[$fieldName] ?? ''), 'formData.{{ $fieldName }}')"
                                    class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                                >
                                    {{-- Toolbar --}}
                                    <div class="flex flex-wrap items-center gap-px border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-1.5">
                                        <select
                                            :value="headingLevel"
                                            @change="setHeading($event.target.value)"
                                            class="h-7 rounded border-0 bg-transparent text-xs text-zinc-700 dark:text-zinc-300 focus:ring-0 cursor-pointer pr-6"
                                        >
                                            <option value="0">Normal</option>
                                            <option value="1">Heading 1</option>
                                            <option value="2">Heading 2</option>
                                            <option value="3">Heading 3</option>
                                        </select>

                                        <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                        <button type="button" @click="cmd().toggleBold().run()" :class="active.bold ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">B</button>
                                        <button type="button" @click="cmd().toggleItalic().run()" :class="active.italic ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm italic text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">I</button>
                                        <button type="button" @click="cmd().toggleUnderline().run()" :class="active.underline ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm underline text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">U</button>
                                        <button type="button" @click="cmd().toggleStrike().run()" :class="active.strike ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm line-through text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">S</button>

                                        <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                        <button type="button" @click="cmd().toggleBlockquote().run()" :class="active.blockquote ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 font-mono">&ldquo;</button>
                                        <button type="button" @click="cmd().toggleCodeBlock().run()" :class="active.codeBlock ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 font-mono">&lt;/&gt;</button>

                                        <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                        <button type="button" @click="cmd().toggleBulletList().run()" :class="active.bulletList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">• List</button>
                                        <button type="button" @click="cmd().toggleOrderedList().run()" :class="active.orderedList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">1. List</button>

                                        <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                        <button type="button" @click="setLink()" :class="active.link ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">Link</button>
                                        <button type="button" @click="cmd().unsetAllMarks().clearNodes().run()" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-600">Clear</button>
                                        <button type="button" @click="toggleSource()" :class="sourceMode ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="ml-auto flex h-7 items-center justify-center rounded px-2 text-xs font-mono text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-600">&lt;/&gt;</button>
                                    </div>

                                    {{-- Editor --}}
                                    <div x-ref="editorEl" class="min-h-64" x-show="!sourceMode"></div>
                                    <textarea x-show="sourceMode" x-model="sourceHtml" class="w-full min-h-64 p-4 font-mono text-sm text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-900 outline-none resize-y border-0"></textarea>
                                </div>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'date')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <flux:input wire:model="formData.{{ $fieldName }}" type="date" :required="$fieldRequired" />
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'select')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <flux:select wire:model="formData.{{ $fieldName }}">
                                    @if (!$fieldRequired)
                                        <flux:select.option value="">— Select —</flux:select.option>
                                    @endif
                                    @foreach ($fieldOptions as $option)
                                        @if ($option !== '')
                                            <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                                        @endif
                                    @endforeach
                                </flux:select>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'toggle')
                            <flux:field>
                                <flux:switch wire:model="formData.{{ $fieldName }}" :label="$fieldLabel" />
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'checkbox')
                            <flux:field>
                                <flux:checkbox wire:model="formData.{{ $fieldName }}" :label="$fieldLabel" />
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'radio')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}</flux:label>
                                <div class="space-y-2 mt-1">
                                    @foreach ($fieldOptions as $option)
                                        @if ($option !== '')
                                            <flux:radio wire:model="formData.{{ $fieldName }}" :value="$option" :label="$option" />
                                        @endif
                                    @endforeach
                                </div>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'checkboxes')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}</flux:label>
                                <div class="space-y-2 mt-1">
                                    @foreach ($fieldOptions as $option)
                                        @if ($option !== '')
                                            <flux:checkbox wire:model="formData.{{ $fieldName }}" :value="$option" :label="$option" />
                                        @endif
                                    @endforeach
                                </div>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'oembed')
                            <flux:field>
                                <flux:label>{{ $fieldLabel }}@if (!$fieldRequired) <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>@endif</flux:label>
                                <flux:input wire:model="formData.{{ $fieldName }}" type="url" placeholder="https://www.youtube.com/watch?v=…" :required="$fieldRequired" />
                                <flux:description>Paste a YouTube, Vimeo, or other oEmbed URL.</flux:description>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </flux:field>

                        @elseif ($fieldType === 'image')
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-3">
                                <flux:label>
                                    {{ $fieldLabel }}
                                    @if (!$fieldRequired)
                                        <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                    @endif
                                </flux:label>

                                @if (!empty($formData[$fieldName]))
                                    <div class="relative group">
                                        <img
                                            src="{{ Storage::disk('public')->url($formData[$fieldName]) }}"
                                            alt="{{ $fieldLabel }}"
                                            class="h-36 w-full object-cover rounded-md"
                                        />
                                        <button
                                            type="button"
                                            wire:click="removeImage('{{ $fieldName }}')"
                                            wire:confirm="Remove this image?"
                                            class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded hover:bg-black/80 transition-colors"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                @endif

                                <div
                                    x-data="{
                                        uploading: false,
                                        handleFile(event) {
                                            const file = event.target.files[0];
                                            if (!file) return;
                                            const maxWidth = 1920;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                const img = new Image();
                                                img.onload = () => {
                                                    if (img.width <= maxWidth) {
                                                        this.uploading = true;
                                                        $wire.upload('imageUploads.{{ $fieldName }}', file, () => {
                                                            $wire.call('uploadImage', '{{ $fieldName }}').then(() => { this.uploading = false; });
                                                        });
                                                        return;
                                                    }
                                                    const scale = maxWidth / img.width;
                                                    const canvas = document.createElement('canvas');
                                                    canvas.width = maxWidth;
                                                    canvas.height = Math.round(img.height * scale);
                                                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                                                    this.uploading = true;
                                                    canvas.toBlob((blob) => {
                                                        $wire.upload('imageUploads.{{ $fieldName }}', new File([blob], file.name, { type: blob.type }), () => {
                                                            $wire.call('uploadImage', '{{ $fieldName }}').then(() => { this.uploading = false; });
                                                        });
                                                    }, file.type, 0.90);
                                                };
                                                img.src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="file"
                                            @change="handleFile($event)"
                                            accept="image/*"
                                            :disabled="uploading"
                                            class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-zinc-300 dark:file:border-zinc-600 file:text-sm file:font-medium file:bg-zinc-50 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-100 dark:hover:file:bg-zinc-700 transition-colors disabled:opacity-50"
                                        />
                                        <span x-show="uploading" class="text-xs text-zinc-500 dark:text-zinc-400 shrink-0">Uploading…</span>
                                    </div>
                                </div>
                                <flux:error name="formData.{{ $fieldName }}" />
                            </div>

                        @elseif ($fieldType === 'gallery')
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-3">
                                <flux:label>
                                    {{ $fieldLabel }}
                                    @if (!$fieldRequired)
                                        <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                    @endif
                                </flux:label>

                                @if (!empty($galleryData[$fieldName]))
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($galleryData[$fieldName] as $gIndex => $gItem)
                                            <div wire:key="gallery-{{ $fieldName }}-{{ $gIndex }}" class="space-y-1">
                                                <div class="relative group aspect-square">
                                                    <img
                                                        src="{{ Storage::disk('public')->url($gItem['path']) }}"
                                                        alt="{{ $gItem['alt'] ?: 'Gallery image ' . ($gIndex + 1) }}"
                                                        class="w-full h-full object-cover rounded-md"
                                                    />
                                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30 rounded-md">
                                                        <button
                                                            type="button"
                                                            wire:click="removeGalleryImage('{{ $fieldName }}', {{ $gIndex }})"
                                                            wire:confirm="Remove this image?"
                                                            class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 transition-colors"
                                                        >Remove</button>
                                                    </div>
                                                </div>
                                                <flux:input wire:model="galleryData.{{ $fieldName }}.{{ $gIndex }}.alt" type="text" placeholder="Alt text…" />
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-center text-zinc-400 dark:text-zinc-500 py-1">No images yet.</p>
                                @endif

                                <div
                                    x-data="{
                                        uploading: false,
                                        handleFile(event) {
                                            const file = event.target.files[0];
                                            if (!file) return;
                                            const maxWidth = 1920;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                const img = new Image();
                                                img.onload = () => {
                                                    if (img.width <= maxWidth) {
                                                        this.uploading = true;
                                                        $wire.upload('newGalleryUpload', file, () => {
                                                            $wire.call('addGalleryImage', '{{ $fieldName }}').then(() => {
                                                                this.uploading = false;
                                                                this.$refs.galleryInput.value = '';
                                                            });
                                                        });
                                                        return;
                                                    }
                                                    const scale = maxWidth / img.width;
                                                    const canvas = document.createElement('canvas');
                                                    canvas.width = maxWidth;
                                                    canvas.height = Math.round(img.height * scale);
                                                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                                                    this.uploading = true;
                                                    canvas.toBlob((blob) => {
                                                        $wire.upload('newGalleryUpload', new File([blob], file.name, { type: blob.type }), () => {
                                                            $wire.call('addGalleryImage', '{{ $fieldName }}').then(() => {
                                                                this.uploading = false;
                                                                this.$refs.galleryInput.value = '';
                                                            });
                                                        });
                                                    }, file.type, 0.90);
                                                };
                                                img.src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="file"
                                            x-ref="galleryInput"
                                            @change="handleFile($event)"
                                            accept="image/*"
                                            :disabled="uploading"
                                            class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-zinc-300 dark:file:border-zinc-600 file:text-sm file:font-medium file:bg-zinc-50 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-100 dark:hover:file:bg-zinc-700 transition-colors disabled:opacity-50"
                                        />
                                        <span x-show="uploading" class="text-xs text-zinc-500 dark:text-zinc-400 shrink-0">Uploading…</span>
                                    </div>
                                </div>
                            </div>

                        @endif
                    @empty
                        <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-sm">
                            No fields defined for this content type. <a href="{{ route('dashboard.content-types.edit', $typeDef->id) }}" wire:navigate class="text-primary hover:underline">Edit the content type</a> to add fields.
                        </div>
                    @endforelse
                </div>

                {{-- Right: sidebar --}}
                <div class="space-y-5 lg:sticky lg:top-4">
                    <flux:button.group class="w-full">
                        <flux:button
                            type="submit"
                            variant="primary"
                            class="flex-1 justify-center"
                            wire:loading.attr="disabled"
                        >
                            Save
                        </flux:button>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="primary" icon="chevron-down" wire:loading.attr="disabled" />
                            <flux:menu>
                                <flux:menu.item wire:click="saveAndExit" icon="arrow-left">Save + Exit</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item :href="route('dashboard.content.index', $typeSlug)" wire:navigate icon="x-mark">Cancel</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:button.group>

                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-4">
                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="draft">Draft</flux:select.option>
                                <flux:select.option value="published">Published</flux:select.option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4">
                        <flux:text class="text-xs text-zinc-400 dark:text-zinc-500">
                            Created {{ $item->created_at->format('M j, Y \a\t g:i A') }}
                        </flux:text>
                        @if ($item->published_at)
                            <flux:text class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                Published {{ $item->published_at->format('M j, Y \a\t g:i A') }}
                            </flux:text>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </flux:main>
</div>

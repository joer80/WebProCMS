<?php

use App\Models\MediaCategory;
use App\Models\MediaItem;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Prop;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Prop]
    public string $fieldKey = '';

    #[Prop]
    public bool $multiSelect = false;

    #[Prop]
    public string $defaultCategorySlug = '';

    public ?int $selectedCategoryId = null;

    public ?int $selectedImageId = null;

    /** @var array<int> */
    public array $selectedImageIds = [];

    public ?int $pendingDeleteImageId = null;

    public bool $showNewCategoryForm = false;

    public string $newCategoryName = '';

    /** @var array<mixed> */
    public array $uploadedImages = [];

    public function mount(): void
    {
        if ($this->defaultCategorySlug !== '') {
            $category = MediaCategory::firstOrCreate(
                ['slug' => $this->defaultCategorySlug],
                ['name' => ucwords(str_replace('-', ' ', $this->defaultCategorySlug)), 'sort_order' => (MediaCategory::max('sort_order') ?? 0) + 1]
            );

            $this->selectedCategoryId = $category->id;
        } else {
            $this->selectedCategoryId = MediaCategory::orderBy('sort_order')->orderBy('id')->value('id');
        }
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, MediaCategory> */
    #[Computed]
    public function categories(): \Illuminate\Database\Eloquent\Collection
    {
        return MediaCategory::query()
            ->withCount('items')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, MediaItem> */
    #[Computed]
    public function images(): \Illuminate\Database\Eloquent\Collection
    {
        return MediaItem::query()
            ->when(
                $this->selectedCategoryId !== null,
                fn ($q) => $q->where('media_category_id', $this->selectedCategoryId)
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function updatedUploadedImages(): void
    {
        $this->validate(['uploadedImages.*' => ['mimes:jpg,jpeg,png,gif,webp,svg,avif', 'max:51200']]);

        $category = $this->selectedCategoryId
            ? MediaCategory::find($this->selectedCategoryId)
            : null;

        if (! $category) {
            return;
        }

        $nextSort = ($category->items()->max('sort_order') ?? 0) + 1;

        foreach ($this->uploadedImages as $file) {
            $path = $file->store($category->slug, 'public');

            ImageResizer::resizeToMaxWidth($path);

            MediaItem::create([
                'media_category_id' => $category->id,
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'alt' => '',
                'sort_order' => $nextSort++,
                'size' => Storage::disk('public')->size($path),
                'mime_type' => $file->getMimeType() ?? 'image/jpeg',
            ]);
        }

        $count = count($this->uploadedImages);
        $this->uploadedImages = [];
        unset($this->images, $this->categories);
        $this->dispatch('notify', message: $count === 1 ? 'Image uploaded.' : 'Images uploaded.');
    }

    public function createCategory(): void
    {
        $this->validate(['newCategoryName' => ['required', 'string', 'max:80']]);

        $slug = Str::slug($this->newCategoryName);
        $maxSort = MediaCategory::max('sort_order') ?? 0;

        $category = MediaCategory::firstOrCreate(
            ['slug' => $slug],
            ['name' => $this->newCategoryName, 'sort_order' => $maxSort + 1]
        );

        unset($this->categories);

        $this->newCategoryName = '';
        $this->showNewCategoryForm = false;
        $this->selectCategory($category->id);
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->selectedImageId = null;
        $this->selectedImageIds = [];
        unset($this->images);
    }

    public function deleteImage(int $id): void
    {
        $item = MediaItem::find($id);

        if (! $item) {
            return;
        }

        Storage::disk('public')->delete($item->path);
        $item->delete();

        if ($this->selectedImageId === $id) {
            $this->selectedImageId = null;
        }

        $this->selectedImageIds = array_values(array_filter($this->selectedImageIds, fn ($i) => $i !== $id));

        unset($this->images, $this->categories);
    }

    public bool $generatingAlt = false;

    public function saveAlt(int $id, string $alt): void
    {
        MediaItem::where('id', $id)->update(['alt' => $alt]);
        unset($this->images);
    }

    public function generateAltForImage(int $id): void
    {
        $item = MediaItem::find($id);

        if (! $item || ! Storage::disk('public')->exists($item->path)) {
            return;
        }

        $this->generatingAlt = true;

        $imageContents = Storage::disk('public')->get($item->path);
        $mimeType = Storage::disk('public')->mimeType($item->path) ?: 'image/jpeg';
        $base64 = base64_encode($imageContents);

        $provider = \App\Models\Setting::get('ai.text_provider', 'claude');
        $prompt = 'Generate a concise, descriptive alt text for this image suitable for screen readers. Maximum 10 words. Return only the alt text, no quotes, no trailing punctuation, no explanation.';

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 100,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'image_url', 'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $base64]],
                            ['type' => 'text', 'text' => $prompt],
                        ],
                    ]],
                ]);

                $alt = $response->json('choices.0.message.content', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 100,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $mimeType, 'data' => $base64]],
                            ['type' => 'text', 'text' => $prompt],
                        ],
                    ]],
                ]);

                $alt = $response->json('content.0.text', '');
            }

            $this->saveAlt($id, trim($alt));
        } catch (\Exception) {
            // silently fail — user can type alt text manually
        }

        $this->generatingAlt = false;
    }

    public function pickImage(int $id, string $path): void
    {
        $item = MediaItem::find($id);
        $this->dispatch('media-image-picked', key: $this->fieldKey, path: $path, alt: $item?->alt ?? '');
    }

    public function toggleMultiImage(int $id): void
    {
        if (in_array($id, $this->selectedImageIds, true)) {
            $this->selectedImageIds = array_values(array_filter($this->selectedImageIds, fn ($i) => $i !== $id));
        } else {
            $this->selectedImageIds[] = $id;
        }
    }

    public function pickMultiImages(): void
    {
        $images = $this->images
            ->whereIn('id', $this->selectedImageIds)
            ->map(fn (MediaItem $item) => ['path' => $item->path, 'alt' => $item->alt ?? ''])
            ->values()
            ->all();

        $this->dispatch('media-images-picked', key: $this->fieldKey, images: $images);
    }
}; ?>

<div class="flex h-[80vh]">
    {{-- Category sidebar --}}
    <div class="w-44 shrink-0 border-r border-zinc-200 dark:border-zinc-700 flex flex-col overflow-y-auto">
        <div class="p-3 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading size="sm" class="text-zinc-500 uppercase tracking-wider">Categories</flux:heading>
        </div>
        <nav class="flex-1 p-2 space-y-0.5">
            <button
                wire:click="selectCategory(null)"
                class="w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded text-sm transition-colors {{ $selectedCategoryId === null ? 'bg-zinc-200 dark:bg-zinc-700 font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
            >
                <span class="truncate">All Media</span>
            </button>

            @foreach ($this->categories as $category)
                <button
                    wire:key="picker-cat-{{ $category->id }}"
                    wire:click="selectCategory({{ $category->id }})"
                    class="w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded text-sm transition-colors {{ $selectedCategoryId === $category->id ? 'bg-zinc-200 dark:bg-zinc-700 font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                >
                    <span class="truncate">{{ $category->name }}</span>
                    <flux:badge size="sm" variant="zinc">{{ $category->items_count }}</flux:badge>
                </button>
            @endforeach
        </nav>

        {{-- New category --}}
        <div class="p-2 border-t border-zinc-200 dark:border-zinc-700 shrink-0">
            @if ($showNewCategoryForm)
                <form wire:submit="createCategory" class="space-y-1.5">
                    <input
                        wire:model="newCategoryName"
                        type="text"
                        placeholder="Category name"
                        autofocus
                        class="w-full text-sm rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                    @error('newCategoryName')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex gap-1.5">
                        <flux:button type="submit" variant="primary" size="sm" class="flex-1">Create</flux:button>
                        <flux:button type="button" variant="ghost" size="sm" wire:click="$set('showNewCategoryForm', false)">Cancel</flux:button>
                    </div>
                </form>
            @else
                <button
                    wire:click="$set('showNewCategoryForm', true)"
                    class="w-full flex items-center gap-1.5 px-2.5 py-1.5 rounded text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <flux:icon name="plus" class="size-3.5 shrink-0" />
                    New category
                </button>
            @endif
        </div>
    </div>

    {{-- Image grid --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden" x-data="{}">
        <div class="flex-1 overflow-y-auto p-3">
            @if ($this->images->isEmpty())
                <div class="flex flex-col items-center justify-center h-full py-12 text-zinc-400 dark:text-zinc-600">
                    <flux:icon name="photo" class="size-10 mb-2 opacity-40" />
                    <p class="text-sm">No images here yet.</p>
                </div>
            @else
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach ($this->images as $image)
                        @if ($multiSelect)
                            <div wire:key="picker-img-{{ $image->id }}" class="group relative rounded-lg overflow-hidden border transition-all aspect-square {{ in_array($image->id, $selectedImageIds) ? 'border-blue-500 ring-2 ring-blue-500' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500' }}">
                                <button
                                    wire:click="toggleMultiImage({{ $image->id }})"
                                    class="absolute inset-0 w-full h-full"
                                >
                                    <img
                                        src="{{ $image->url() }}"
                                        alt="{{ $image->alt }}"
                                        class="w-full h-full object-contain p-1"
                                        loading="lazy"
                                    >
                                </button>
                                @if (in_array($image->id, $selectedImageIds))
                                    <div class="absolute top-1 right-1 size-5 bg-blue-500 rounded-full flex items-center justify-center pointer-events-none">
                                        <flux:icon name="check" class="size-3 text-white" />
                                    </div>
                                @endif
                                @if ($image->alt)
                                    <div class="absolute bottom-0 inset-x-0 bg-zinc-900/60 px-1.5 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                        <p class="text-xs text-white truncate">{{ $image->alt }}</p>
                                    </div>
                                @endif
                                <button
                                    x-on:click.stop="$wire.set('pendingDeleteImageId', {{ $image->id }}); $flux.modal('confirm-delete-image').show()"
                                    class="absolute top-1 left-1 size-6 rounded-full bg-zinc-900/70 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center hover:bg-red-600"
                                >
                                    <flux:icon name="x-mark" class="size-3" />
                                </button>
                            </div>
                        @else
                            <div wire:key="picker-img-{{ $image->id }}" class="group relative rounded-lg overflow-hidden border transition-all aspect-square {{ $selectedImageId === $image->id ? 'border-blue-500 ring-2 ring-blue-500' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500' }}">
                                <button
                                    wire:click="$set('selectedImageId', {{ $image->id }})"
                                    class="absolute inset-0 w-full h-full"
                                >
                                    <img
                                        src="{{ $image->url() }}"
                                        alt="{{ $image->alt }}"
                                        class="w-full h-full object-contain p-1"
                                        loading="lazy"
                                    >
                                </button>
                                @if ($image->alt)
                                    <div class="absolute bottom-0 inset-x-0 bg-zinc-900/60 px-1.5 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                        <p class="text-xs text-white truncate">{{ $image->alt }}</p>
                                    </div>
                                @endif
                                <button
                                    x-on:click.stop="$wire.set('pendingDeleteImageId', {{ $image->id }}); $flux.modal('confirm-delete-image').show()"
                                    class="absolute top-1 left-1 size-6 rounded-full bg-zinc-900/70 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center hover:bg-red-600"
                                >
                                    <flux:icon name="x-mark" class="size-3" />
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Footer with action --}}
        <div class="border-t border-zinc-200 dark:border-zinc-700 px-4 py-3 flex items-center justify-between gap-3 bg-zinc-50 dark:bg-zinc-900 shrink-0">
            {{-- Hidden upload input, triggered via $refs --}}
            <input x-ref="uploadInput" type="file" wire:model="uploadedImages" multiple accept="image/*" class="hidden">

            @if ($multiSelect)
                <span class="text-sm text-zinc-500 dark:text-zinc-400 min-w-0 truncate">
                    {{ count($selectedImageIds) > 0 ? count($selectedImageIds).' image'.( count($selectedImageIds) > 1 ? 's' : '').' selected' : 'Click images to select' }}
                </span>
                <div class="flex items-center gap-2 shrink-0">
                    @if ($selectedCategoryId)
                        <flux:button
                            type="button"
                            variant="outline"
                            size="sm"
                            icon="arrow-up-tray"
                            x-on:click="$refs.uploadInput.click()"
                            wire:loading.attr="disabled"
                            wire:target="uploadedImages"
                        >
                            <span wire:loading.remove wire:target="uploadedImages">Upload</span>
                            <span wire:loading wire:target="uploadedImages">Uploading…</span>
                        </flux:button>
                    @endif
                    <flux:button
                        wire:click="pickMultiImages"
                        variant="primary"
                        size="sm"
                        :disabled="empty($selectedImageIds)"
                    >
                        Add {{ count($selectedImageIds) > 0 ? count($selectedImageIds) : '' }} {{ count($selectedImageIds) === 1 ? 'Image' : 'Images' }}
                    </flux:button>
                </div>
            @else
                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $selectedImageId ? '' : 'Click an image to select it' }}
                </span>
                @if ($selectedCategoryId)
                    <flux:button
                        type="button"
                        variant="outline"
                        size="sm"
                        icon="arrow-up-tray"
                        x-on:click="$refs.uploadInput.click()"
                        wire:loading.attr="disabled"
                        wire:target="uploadedImages"
                    >
                        <span wire:loading.remove wire:target="uploadedImages">Upload</span>
                        <span wire:loading wire:target="uploadedImages">Uploading…</span>
                    </flux:button>
                @endif
            @endif
        </div>
    </div>

    @if (!$multiSelect && $selectedImageId)
        @php $previewImg = $this->images->firstWhere('id', $selectedImageId); @endphp
        <div class="w-56 shrink-0 border-l border-zinc-200 dark:border-zinc-700 flex flex-col bg-white dark:bg-zinc-900">
            {{-- Image preview --}}
            <div class="p-3 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 overflow-hidden aspect-square shrink-0">
                <img src="{{ $previewImg?->url() }}" alt="{{ $previewImg?->alt }}" class="max-w-full max-h-full object-contain rounded">
            </div>

            {{-- Info + actions --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-3 border-t border-zinc-200 dark:border-zinc-700">
                <div>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 truncate mb-0.5">{{ $previewImg?->filename }}</p>
                    <p class="text-xs font-mono text-zinc-600 dark:text-zinc-400 break-all bg-zinc-100 dark:bg-zinc-800 rounded p-1.5 leading-relaxed select-all">{{ $previewImg?->url() }}</p>
                    <div x-data="{ copied: false }" class="mt-1.5">
                        <button
                            @click="navigator.clipboard.writeText('{{ addslashes($previewImg?->url()) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="w-full flex items-center justify-center gap-1.5 text-xs px-2 py-1.5 rounded-md border border-zinc-200 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                        >
                            <flux:icon name="clipboard" class="size-3.5" x-show="!copied" />
                            <flux:icon name="check" class="size-3.5 text-green-500" x-show="copied" x-cloak />
                            <span x-show="!copied">Copy URL</span>
                            <span x-show="copied" x-cloak class="text-green-600 dark:text-green-400">Copied!</span>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Alt text</label>
                        @if (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'))
                            <button
                                type="button"
                                wire:click="generateAltForImage({{ $selectedImageId }})"
                                wire:loading.attr="disabled"
                                wire:target="generateAltForImage"
                                class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors disabled:opacity-50"
                                title="Generate alt text from image"
                            >
                                <span wire:loading.remove wire:target="generateAltForImage"><flux:icon name="sparkles" class="size-3.5" /></span>
                                <span wire:loading wire:target="generateAltForImage">
                                    <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        @endif
                    </div>
                    <input
                        type="text"
                        value="{{ $previewImg?->alt }}"
                        x-on:change="$wire.saveAlt({{ $selectedImageId }}, $event.target.value)"
                        placeholder="Describe this image…"
                        class="w-full text-xs rounded border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                </div>
            </div>

            {{-- Use This Image button --}}
            <div class="shrink-0 p-3 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button
                    wire:click="pickImage({{ $selectedImageId }}, '{{ $previewImg?->path }}')"
                    variant="primary"
                    size="sm"
                    class="w-full"
                >
                    Use This Image
                </flux:button>
            </div>
        </div>
    @endif

    <flux:modal name="confirm-delete-image" class="w-full max-w-sm">
        <flux:heading size="lg">Delete image?</flux:heading>
        <flux:text class="mt-2">This will permanently delete the image and cannot be undone.</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="deleteImage($wire.pendingDeleteImageId)">Delete</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>
</div>

<?php

use App\Models\MediaCategory;
use App\Models\MediaItem;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Media Library')] class extends Component {
    use WithFileUploads;

    public ?int $selectedCategoryId = null;

    public array $selectedImageIds = [];

    /** @var array<int, mixed> */
    public array $newImages = [];

    public ?int $editingAltId = null;

    public string $editingAltValue = '';

    public string $newCategoryName = '';

    public bool $showNewCategoryForm = false;

    public ?int $renamingCategoryId = null;

    public string $renamingCategoryName = '';

    public ?int $confirmingDeleteCategory = null;

    public ?int $confirmingDeleteImage = null;

    public bool $confirmingBulkDelete = false;

    public ?int $previewImageId = null;

    public bool $showGenerateModal = false;

    public string $generatePrompt = '';

    public bool $generatingImage = false;

    public ?string $generatedTempPath = null;

    public string $generateError = '';

    public string $lastGeneratedPrompt = '';

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

    #[Computed]
    public function totalCount(): int
    {
        return MediaItem::query()->count();
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->selectedImageIds = [];
        $this->previewImageId = null;
        $this->confirmingDeleteImage = null;
        $this->confirmingBulkDelete = false;
        unset($this->images);
    }

    public function toggleImage(int $id): void
    {
        $this->previewImageId = $id;

        if (in_array($id, $this->selectedImageIds)) {
            $this->selectedImageIds = array_values(
                array_filter($this->selectedImageIds, fn (int $i) => $i !== $id)
            );
        } else {
            $this->selectedImageIds[] = $id;
        }
    }

    public function closePreview(): void
    {
        $this->previewImageId = null;
    }

    public function selectAll(): void
    {
        $this->selectedImageIds = $this->images->pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedImageIds = [];
    }

    public function updatedNewImages(): void
    {
        $this->validate(['newImages.*' => 'image|max:51200']);

        $category = $this->selectedCategoryId
            ? MediaCategory::query()->findOrFail($this->selectedCategoryId)
            : MediaCategory::query()->where('is_default', true)->firstOrFail();

        $nextSortOrder = MediaItem::query()
            ->where('media_category_id', $category->id)
            ->max('sort_order') ?? 0;

        $count = 0;

        foreach ($this->newImages as $file) {
            ImageResizer::resizeAbsolutePath($file->getRealPath());
            $path = $file->store($category->slug, 'media');
            $size = filesize($file->getRealPath()) ?: 0;

            MediaItem::create([
                'media_category_id' => $category->id,
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'alt' => '',
                'sort_order' => ++$nextSortOrder,
                'size' => $size,
                'mime_type' => $file->getMimeType() ?? 'image/jpeg',
            ]);

            $count++;
        }

        $this->newImages = [];
        unset($this->images, $this->categories, $this->totalCount);

        $this->dispatch('notify', message: $count === 1 ? '1 image uploaded.' : "{$count} images uploaded.");
    }

    public function startEditingAlt(int $id, string $currentAlt): void
    {
        $this->editingAltId = $id;
        $this->editingAltValue = $currentAlt;
    }

    public bool $generatingAlt = false;

    public function updateAlt(int $id, string $alt): void
    {
        MediaItem::query()->findOrFail($id)->update(['alt' => $alt]);
        unset($this->images);
    }

    public function generateAltForImage(int $id): void
    {
        $item = MediaItem::find($id);

        if (! $item || ! Storage::disk('media')->exists($item->path)) {
            return;
        }

        $this->generatingAlt = true;

        $imageContents = Storage::disk('media')->get($item->path);
        $mimeType = Storage::disk('media')->mimeType($item->path) ?: 'image/jpeg';
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

            $this->updateAlt($id, ucfirst(trim($alt)));
        } catch (\Exception) {
            // silently fail — user can type alt text manually
        }

        $this->generatingAlt = false;
    }

    public function saveAlt(int $id): void
    {
        if ($this->editingAltId !== $id) {
            return;
        }

        $this->validate(['editingAltValue' => 'nullable|string|max:255']);

        MediaItem::query()->findOrFail($id)->update(['alt' => $this->editingAltValue]);

        $this->editingAltId = null;
        $this->editingAltValue = '';
        unset($this->images);
    }

    public function cancelEditingAlt(): void
    {
        $this->editingAltId = null;
        $this->editingAltValue = '';
    }

    public function moveToCategory(int $categoryId, ?int $imageId = null): void
    {
        if ($imageId !== null && in_array($imageId, $this->selectedImageIds)) {
            $idsToMove = $this->selectedImageIds;
        } elseif ($imageId !== null) {
            $idsToMove = [$imageId];
        } else {
            $idsToMove = $this->selectedImageIds;
        }

        if (empty($idsToMove)) {
            return;
        }

        $nextSortOrder = MediaItem::query()
            ->where('media_category_id', $categoryId)
            ->max('sort_order') ?? 0;

        foreach ($idsToMove as $index => $id) {
            MediaItem::query()->findOrFail($id)->update([
                'media_category_id' => $categoryId,
                'sort_order' => $nextSortOrder + $index + 1,
            ]);
        }

        $this->selectedImageIds = [];
        unset($this->images, $this->categories);
    }

    public function reorderImages(int $fromId, int $toId): void
    {
        $from = MediaItem::query()->findOrFail($fromId);
        $to = MediaItem::query()->findOrFail($toId);

        if ($from->media_category_id !== $to->media_category_id) {
            return;
        }

        [$from->sort_order, $to->sort_order] = [$to->sort_order, $from->sort_order];
        $from->save();
        $to->save();

        unset($this->images);
    }

    public function deleteImage(int $id): void
    {
        MediaItem::query()->findOrFail($id)->delete();

        $this->confirmingDeleteImage = null;

        if ($this->previewImageId === $id) {
            $this->previewImageId = null;
        }

        $this->selectedImageIds = array_values(
            array_filter($this->selectedImageIds, fn (int $i) => $i !== $id)
        );
        unset($this->images, $this->categories, $this->totalCount);
    }

    public function deleteSelected(): void
    {
        $count = count($this->selectedImageIds);

        MediaItem::query()
            ->whereIn('id', $this->selectedImageIds)
            ->get()
            ->each->delete();

        $this->selectedImageIds = [];
        $this->confirmingBulkDelete = false;
        unset($this->images, $this->categories, $this->totalCount);

        $this->dispatch('notify', message: "{$count} image(s) deleted.");
    }

    public function createCategory(): void
    {
        $this->validate(['newCategoryName' => 'required|string|max:255']);

        $nextOrder = MediaCategory::query()->max('sort_order') ?? 0;

        MediaCategory::create([
            'name' => $this->newCategoryName,
            'sort_order' => $nextOrder + 1,
        ]);

        $this->newCategoryName = '';
        $this->showNewCategoryForm = false;
        unset($this->categories);
    }

    public function startRenamingCategory(int $id, string $name): void
    {
        $this->renamingCategoryId = $id;
        $this->renamingCategoryName = $name;
        $this->confirmingDeleteCategory = null;
    }

    public function renameCategory(): void
    {
        $this->validate(['renamingCategoryName' => 'required|string|max:255']);

        MediaCategory::query()
            ->findOrFail($this->renamingCategoryId)
            ->update(['name' => $this->renamingCategoryName]);

        $this->renamingCategoryId = null;
        $this->renamingCategoryName = '';
        unset($this->categories);
    }

    public function deleteCategory(int $id): void
    {
        $category = MediaCategory::query()->findOrFail($id);

        if ($category->is_default) {
            return;
        }

        $defaultId = MediaCategory::query()->where('is_default', true)->value('id');

        MediaItem::query()
            ->where('media_category_id', $id)
            ->update(['media_category_id' => $defaultId]);

        $category->delete();

        if ($this->selectedCategoryId === $id) {
            $this->selectedCategoryId = null;
        }

        $this->confirmingDeleteCategory = null;
        unset($this->categories, $this->images);
    }

    public function reorderCategories(int $fromId, int $toId): void
    {
        $from = MediaCategory::query()->findOrFail($fromId);
        $to = MediaCategory::query()->findOrFail($toId);

        [$from->sort_order, $to->sort_order] = [$to->sort_order, $from->sort_order];
        $from->save();
        $to->save();

        unset($this->categories);
    }

    public function openGenerateModal(): void
    {
        $this->showGenerateModal = true;
        $this->generatePrompt = '';
        $this->lastGeneratedPrompt = '';
        $this->generatedTempPath = null;
        $this->generateError = '';
    }

    public function updatedShowGenerateModal(bool $value): void
    {
        if (! $value && $this->generatedTempPath) {
            Storage::disk('public')->delete($this->generatedTempPath);
            $this->generatedTempPath = null;
        }
    }

    public function generateImageForLibrary(): void
    {
        $this->validate(['generatePrompt' => 'required|string|max:1000']);

        if ($this->generatedTempPath) {
            Storage::disk('public')->delete($this->generatedTempPath);
            $this->generatedTempPath = null;
        }

        $this->generateError = '';
        $provider = \App\Models\Setting::get('ai.image_provider', 'openai');

        try {
            $imageContents = match ($provider) {
                'fal' => $this->generateImageViaFal($this->generatePrompt),
                'stability' => $this->generateImageViaStability($this->generatePrompt),
                default => $this->generateImageViaOpenAi($this->generatePrompt),
            };
        } catch (\Exception $e) {
            $this->generateError = $e->getMessage();

            return;
        }

        $tempPath = 'tmp/ai-' . now()->format('YmdHis') . '-' . substr(md5($this->generatePrompt), 0, 6) . '.jpg';
        Storage::disk('public')->put($tempPath, $imageContents);
        $this->lastGeneratedPrompt = $this->generatePrompt;
        $this->generatedTempPath = $tempPath;
    }

    public function saveGeneratedImage(): void
    {
        if (! $this->generatedTempPath || ! Storage::disk('public')->exists($this->generatedTempPath)) {
            return;
        }

        $category = $this->selectedCategoryId
            ? MediaCategory::query()->findOrFail($this->selectedCategoryId)
            : MediaCategory::query()->where('is_default', true)->first()
                ?? MediaCategory::query()->first();

        $filename = basename($this->generatedTempPath);
        $finalPath = $category->slug . '/' . $filename;

        Storage::disk('public')->move($this->generatedTempPath, $finalPath);

        $nextSortOrder = MediaItem::query()
            ->where('media_category_id', $category->id)
            ->max('sort_order') ?? 0;

        MediaItem::create([
            'media_category_id' => $category->id,
            'path' => $finalPath,
            'filename' => $filename,
            'alt' => $this->generatePrompt,
            'sort_order' => $nextSortOrder + 1,
            'size' => Storage::disk('public')->size($finalPath),
            'mime_type' => 'image/jpeg',
        ]);

        $this->generatedTempPath = null;
        $this->generatePrompt = '';
        $this->showGenerateModal = false;
        unset($this->images, $this->categories, $this->totalCount);

        $this->dispatch('notify', message: 'Image saved to library.');
    }

    public function discardGeneratedImage(): void
    {
        if ($this->generatedTempPath) {
            Storage::disk('public')->delete($this->generatedTempPath);
            $this->generatedTempPath = null;
        }

        $this->showGenerateModal = false;
        $this->generatePrompt = '';
        $this->lastGeneratedPrompt = '';
        $this->generateError = '';
    }

    protected function generateImageViaOpenAi(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.openai_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No OpenAI API key configured.');
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/images/generations', [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1792x1024',
            'response_format' => 'url',
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('error.message') ?? 'OpenAI image generation failed.');
        }

        $imageUrl = $response->json('data.0.url');

        if (empty($imageUrl)) {
            throw new \Exception('No image URL returned from OpenAI.');
        }

        $imageResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw new \Exception('Failed to download generated image from OpenAI.');
        }

        return $imageResponse->body();
    }

    protected function generateImageViaFal(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.fal_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No fal.ai API key configured.');
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Key ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://fal.run/fal-ai/flux/schnell', [
            'prompt' => $prompt,
            'image_size' => 'landscape_16_9',
            'num_inference_steps' => 4,
            'num_images' => 1,
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('detail') ?? $response->json('error') ?? 'fal.ai image generation failed.');
        }

        $imageUrl = $response->json('images.0.url');

        if (empty($imageUrl)) {
            throw new \Exception('No image URL returned from fal.ai.');
        }

        $imageResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw new \Exception('Failed to download generated image from fal.ai.');
        }

        return $imageResponse->body();
    }

    protected function generateImageViaStability(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.stability_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No Stability AI API key configured.');
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->timeout(60)->asMultipart()->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
            ['name' => 'prompt', 'contents' => $prompt],
            ['name' => 'output_format', 'contents' => 'jpeg'],
            ['name' => 'aspect_ratio', 'contents' => '16:9'],
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('errors.0') ?? $response->json('message') ?? 'Stability AI image generation failed.');
        }

        $base64 = $response->json('image');

        if (empty($base64)) {
            throw new \Exception('No image data returned from Stability AI.');
        }

        return base64_decode($base64);
    }
}; ?>

<div>
    <flux:main class="p-0!">
        <div
            x-data="{ draggingId: null, overCategoryId: null }"
            class="flex min-h-screen"
        >
            {{-- ───── LEFT: Category Sidebar ───── --}}
            <aside class="w-56 shrink-0 sticky top-0 h-screen flex flex-col border-r border-zinc-200 dark:border-zinc-700">

                {{-- Header --}}
                <div class="px-4 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="lg">Media Library</flux:heading>
                </div>

                {{-- Nav (scrollable) --}}
                <nav class="flex-1 min-h-0 p-2 space-y-0.5 overflow-y-auto">
                    {{-- All Media --}}
                    <button
                        wire:click="selectCategory(null)"
                        class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-md text-sm transition-colors {{ $selectedCategoryId === null ? 'bg-zinc-200 dark:bg-zinc-700 font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                    >
                        <span class="flex items-center gap-2 min-w-0 flex-1">
                            <flux:icon name="photo" class="size-4 shrink-0" />
                            <span class="truncate">All Media</span>
                        </span>
                        <flux:badge size="sm" variant="zinc" class="ml-auto shrink-0">{{ $this->totalCount }}</flux:badge>
                    </button>

                    {{-- Uncategorized (default) always sits directly below All Media --}}
                    @foreach ($this->categories->where('is_default', true) as $category)
                        <div
                            wire:key="cat-{{ $category->id }}"
                            @dragover.prevent="overCategoryId = {{ $category->id }}"
                            @dragleave="if (!$el.contains($event.relatedTarget)) overCategoryId = null"
                            @drop.prevent="$wire.moveToCategory({{ $category->id }}, draggingId); draggingId = null; overCategoryId = null"
                            :class="overCategoryId === {{ $category->id }} ? 'ring-2 ring-inset ring-blue-500 rounded-md' : ''"
                        >
                            <div class="relative group rounded-md">
                                <button
                                    wire:click="selectCategory({{ $category->id }})"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-md text-sm transition-colors min-w-0 {{ $selectedCategoryId === $category->id ? 'bg-zinc-200 dark:bg-zinc-700 font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                                >
                                    <span class="truncate flex-1">{{ $category->name }}</span>
                                    <flux:badge size="sm" variant="zinc" class="ml-auto shrink-0">{{ $category->items_count }}</flux:badge>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- User-created categories --}}
                    <div class="pt-2 pb-1 px-3 flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Categories</span>
                        <button
                            wire:click="$set('showNewCategoryForm', true)"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors"
                            title="New Category"
                        >
                            <flux:icon name="plus" class="size-3.5" />
                        </button>
                    </div>

                    @foreach ($this->categories->where('is_default', false) as $category)
                        <div
                            wire:key="cat-{{ $category->id }}"
                            class="pl-2"
                            @dragover.prevent="overCategoryId = {{ $category->id }}"
                            @dragleave="if (!$el.contains($event.relatedTarget)) overCategoryId = null"
                            @drop.prevent="$wire.moveToCategory({{ $category->id }}, draggingId); draggingId = null; overCategoryId = null"
                            :class="overCategoryId === {{ $category->id }} ? 'ring-2 ring-inset ring-blue-500 rounded-md' : ''"
                        >
                            @if ($renamingCategoryId === $category->id)
                                <form wire:submit="renameCategory" class="px-1 py-1 flex items-center gap-1">
                                    <flux:input
                                        wire:model="renamingCategoryName"
                                        size="sm"
                                        autofocus
                                        class="flex-1 min-w-0"
                                        x-on:keydown.escape="$wire.set('renamingCategoryId', null)"
                                    />
                                    <flux:button type="submit" size="xs" variant="primary" icon="check" />
                                    <flux:button type="button" wire:click="$set('renamingCategoryId', null)" size="xs" variant="ghost" icon="x-mark" />
                                </form>
                                <flux:error name="renamingCategoryName" class="px-2 text-xs" />
                            @else
                                <div class="relative group rounded-md">
                                    <button
                                        wire:click="selectCategory({{ $category->id }})"
                                        class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-md text-sm transition-colors min-w-0 {{ $selectedCategoryId === $category->id ? 'bg-zinc-200 dark:bg-zinc-700 font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                                    >
                                        <span class="truncate flex-1">{{ $category->name }}</span>
                                        <flux:badge size="sm" variant="zinc" class="ml-auto shrink-0 transition-opacity group-hover:opacity-0">{{ $category->items_count }}</flux:badge>
                                    </button>

                                    @if ($confirmingDeleteCategory === $category->id)
                                        <div class="absolute inset-y-0 right-1 flex items-center gap-0.5" wire:click.stop>
                                            <flux:button wire:click="deleteCategory({{ $category->id }})" variant="danger" size="xs">Yes</flux:button>
                                            <flux:button wire:click="$set('confirmingDeleteCategory', null)" variant="ghost" size="xs">No</flux:button>
                                        </div>
                                    @else
                                        <div class="absolute inset-y-0 right-1 flex items-center opacity-0 group-hover:opacity-100 transition-opacity" wire:click.stop>
                                            <flux:button
                                                wire:click="startRenamingCategory({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                                variant="ghost"
                                                size="xs"
                                                icon="pencil"
                                            />
                                            <flux:button
                                                wire:click="$set('confirmingDeleteCategory', {{ $category->id }})"
                                                variant="ghost"
                                                size="xs"
                                                icon="trash"
                                                class="text-red-500 dark:text-red-400"
                                            />
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach

                </nav>
            </aside>

            {{-- ───── RIGHT: Image Grid ───── --}}
            <div class="flex-1 flex flex-col min-w-0">

                {{-- Sticky Toolbar --}}
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 px-5 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <div class="flex items-center gap-3 min-w-0">
                        @if (count($selectedImageIds) > 0)
                            <span class="text-sm text-zinc-600 dark:text-zinc-400 shrink-0">
                                {{ count($selectedImageIds) }} selected
                            </span>
                            <flux:button wire:click="deselectAll" variant="ghost" size="sm">
                                Deselect All
                            </flux:button>
                            @if ($confirmingBulkDelete)
                                <div class="flex items-center gap-1">
                                    <flux:button wire:click="deleteSelected" variant="danger" size="sm">
                                        Delete {{ count($selectedImageIds) }}
                                    </flux:button>
                                    <flux:button wire:click="$set('confirmingBulkDelete', false)" variant="ghost" size="sm">
                                        Cancel
                                    </flux:button>
                                </div>
                            @else
                                <flux:button
                                    wire:click="$set('confirmingBulkDelete', true)"
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-500 dark:text-red-400"
                                >
                                    Delete Selected
                                </flux:button>
                            @endif
                        @else
                            @if ($this->images->isNotEmpty())
                                <flux:button wire:click="selectAll" variant="ghost" size="sm">
                                    Select All
                                </flux:button>
                            @endif
                            @if ($selectedCategoryId !== null)
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                    {{ $this->categories->firstWhere('id', $selectedCategoryId)?->name }}
                                </span>
                            @endif
                        @endif
                    </div>

                    {{-- Add Category + Upload --}}
                    <div class="flex items-center gap-2 shrink-0">
                    <flux:button
                        wire:click="$set('showNewCategoryForm', true)"
                        variant="ghost"
                        size="sm"
                        icon="folder-plus"
                    >
                        Add Category
                    </flux:button>
                    @if (\App\Models\Setting::get('ai.openai_key') || \App\Models\Setting::get('ai.fal_key') || \App\Models\Setting::get('ai.stability_key'))
                    <flux:button
                        wire:click="openGenerateModal"
                        variant="ghost"
                        size="sm"
                        icon="sparkles"
                    >
                        Generate Image
                    </flux:button>
                    @endif
                    <div x-data="{ uploading: false }" class="shrink-0"
                        x-on:livewire-upload-start.window="uploading = true"
                        x-on:livewire-upload-finish.window="uploading = false"
                        x-on:livewire-upload-error.window="uploading = false">
                        <flux:button
                            variant="primary"
                            size="sm"
                            icon="arrow-up-tray"
                            x-on:click="if (!uploading) $refs.mediaUpload.click()"
                        >
                            <span x-show="!uploading">Upload Images</span>
                            <span x-show="uploading" x-cloak>Uploading…</span>
                        </flux:button>
                        <input
                            x-ref="mediaUpload"
                            wire:model="newImages"
                            type="file"
                            multiple
                            accept="image/*"
                            class="hidden"
                        >
                    </div>
                    </div>
                </div>

                {{-- Loading bar --}}
                <div x-data="{ uploading: false }" x-show="uploading" x-cloak class="h-0.5 bg-blue-500 animate-pulse"
                    x-on:livewire-upload-start.window="uploading = true"
                    x-on:livewire-upload-finish.window="uploading = false"
                    x-on:livewire-upload-error.window="uploading = false"
                ></div>

                {{-- Image Grid --}}
                <div class="flex-1 p-5">
                    @if ($this->images->isEmpty())
                        <div class="flex flex-col items-center justify-center py-24 text-zinc-400 dark:text-zinc-600">
                            <flux:icon name="photo" class="size-12 mb-3 opacity-40" />
                            <p class="text-sm">
                                @if ($selectedCategoryId !== null)
                                    No images in this category yet.
                                @else
                                    No images yet. Upload some to get started!
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                            @foreach ($this->images as $image)
                                <div
                                    wire:key="img-{{ $image->id }}"
                                    draggable="true"
                                    @dragstart.stop="draggingId = {{ $image->id }}"
                                    @dragend="draggingId = null"
                                    @dragover.prevent
                                    @drop.stop.prevent="if (draggingId !== null && draggingId !== {{ $image->id }}) $wire.reorderImages(draggingId, {{ $image->id }})"
                                    :style="{ opacity: draggingId === {{ $image->id }} ? '0.4' : '1' }"
                                    wire:click="toggleImage({{ $image->id }})"
                                    class="group relative rounded-lg overflow-hidden border cursor-pointer select-none transition-all {{ in_array($image->id, $selectedImageIds) ? 'border-blue-500 ring-2 ring-blue-500' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500' }}"
                                >
                                    {{-- Thumbnail --}}
                                    <div class="aspect-square bg-zinc-100 dark:bg-zinc-800">
                                        <img
                                            src="{{ $image->url() }}"
                                            alt="{{ $image->alt }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>

                                    {{-- Checkbox overlay --}}
                                    <div class="absolute top-1.5 left-1.5 pointer-events-none">
                                        <div class="size-5 rounded border-2 flex items-center justify-center transition-colors {{ in_array($image->id, $selectedImageIds) ? 'bg-blue-500 border-blue-500' : 'bg-white/80 dark:bg-zinc-900/80 border-zinc-300 dark:border-zinc-500 group-hover:border-zinc-500' }}">
                                            @if (in_array($image->id, $selectedImageIds))
                                                <flux:icon name="check" class="size-3 text-white" />
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Drag handle --}}
                                    <div class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity bg-white/80 dark:bg-zinc-900/80 rounded p-0.5 cursor-grab pointer-events-none">
                                        <flux:icon.grip-vertical class="size-4 text-zinc-500" />
                                    </div>

                                    {{-- Delete button --}}
                                    @if ($confirmingDeleteImage === $image->id)
                                        <div class="absolute top-1.5 right-1.5 flex gap-0.5 z-10" wire:click.stop>
                                            <button
                                                wire:click.stop="deleteImage({{ $image->id }})"
                                                class="px-1.5 py-0.5 text-xs bg-red-500 text-white rounded font-medium hover:bg-red-600"
                                            >Delete</button>
                                            <button
                                                wire:click.stop="$set('confirmingDeleteImage', null)"
                                                class="px-1.5 py-0.5 text-xs bg-zinc-600 text-white rounded hover:bg-zinc-700"
                                            >✕</button>
                                        </div>
                                    @else
                                        <button
                                            wire:click.stop="$set('confirmingDeleteImage', {{ $image->id }})"
                                            class="absolute top-1.5 right-1.5 z-10 opacity-0 group-hover:opacity-100 transition-opacity bg-red-500 text-white rounded p-0.5 hover:bg-red-600"
                                        >
                                            <flux:icon name="trash" class="size-3.5" />
                                        </button>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        {{-- ───── RIGHT: Preview Panel ───── --}}
        @if ($previewImageId)
            @php $previewImage = $this->images->firstWhere('id', $previewImageId); @endphp
            <div class="w-72 shrink-0 sticky top-0 h-screen flex flex-col border-l border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                {{-- Header --}}
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between shrink-0">
                    <flux:heading size="sm">Preview</flux:heading>
                    <button
                        wire:click="closePreview"
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors"
                    >
                        <flux:icon name="x-mark" class="size-4" />
                    </button>
                </div>

                {{-- Image --}}
                <div class="bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center overflow-hidden aspect-square shrink-0">
                    <img
                        src="{{ $previewImage?->url() }}"
                        alt="{{ $previewImage?->alt }}"
                        class="max-w-full max-h-full object-contain p-2"
                    >
                </div>

                {{-- Info --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Filename</p>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 break-all">{{ $previewImage?->filename }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">URL</p>
                        <p class="text-xs font-mono text-zinc-600 dark:text-zinc-400 break-all bg-zinc-100 dark:bg-zinc-800 rounded p-2 leading-relaxed select-all">{{ $previewImage?->url() }}</p>
                        <div x-data="{ copied: false }" class="mt-1.5">
                            <button
                                @click="navigator.clipboard.writeText('{{ addslashes($previewImage?->url()) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                class="w-full flex items-center justify-center gap-1.5 text-xs px-3 py-1.5 rounded-md border border-zinc-200 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
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
                            <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Alt Text</p>
                            @if (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'))
                                <button
                                    type="button"
                                    wire:click="generateAltForImage({{ $previewImageId }})"
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
                            value="{{ $previewImage?->alt }}"
                            x-on:change="$wire.updateAlt({{ $previewImageId }}, $event.target.value)"
                            placeholder="Describe this image…"
                            class="w-full text-sm rounded border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary"
                        >
                    </div>

                    @if ($previewImage?->size)
                        <div>
                            <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Size</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($previewImage->size / 1024, 1) }} KB</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        </div>
    </flux:main>

    <flux:modal wire:model="showNewCategoryForm" class="w-80">
        <flux:heading size="lg">New Category</flux:heading>
        <form wire:submit="createCategory" class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="newCategoryName" placeholder="Category name" autofocus />
                <flux:error name="newCategoryName" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showNewCategoryForm', false)" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showGenerateModal" class="w-2xl">
        <flux:heading size="lg">Generate Image with AI</flux:heading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Describe the image you want to create.
        </p>

        <div class="mt-5 space-y-4">
            <flux:field>
                <flux:label>Prompt</flux:label>
                <flux:textarea
                    wire:model="generatePrompt"
                    rows="3"
                    placeholder="A tree in a park, photorealistic, natural lighting, detailed bark…"
                />
                <flux:error name="generatePrompt" />
                @if ($lastGeneratedPrompt)
                    <flux:description>Keep your original description and add style changes at the end — don't replace it entirely.</flux:description>
                @endif
            </flux:field>

            <div class="flex items-center justify-between gap-3">
                <flux:button
                    wire:click="generateImageForLibrary"
                    wire:loading.attr="disabled"
                    wire:target="generateImageForLibrary"
                    variant="{{ $generatedTempPath ? 'ghost' : 'primary' }}"
                    icon="sparkles"
                >
                    <span wire:loading.remove wire:target="generateImageForLibrary">
                        {{ $generatedTempPath ? 'Regenerate' : 'Generate' }}
                    </span>
                    <span wire:loading wire:target="generateImageForLibrary" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Generating…
                    </span>
                </flux:button>

                @if ($generatedTempPath)
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="discardGeneratedImage" variant="ghost">
                            Discard
                        </flux:button>
                        <flux:button wire:click="saveGeneratedImage" variant="primary" icon="arrow-down-tray">
                            Save to Library
                        </flux:button>
                    </div>
                @endif
            </div>

            @if ($generateError)
                <div class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-3 py-2.5 text-sm text-red-700 dark:text-red-300">
                    {{ $generateError }}
                </div>
            @endif

            @if ($generatedTempPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($generatedTempPath))
                <div class="rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($generatedTempPath) }}"
                        alt="Generated image preview"
                        class="w-full"
                    >
                </div>
            @endif
        </div>
    </flux:modal>
</div>

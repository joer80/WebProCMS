<?php

use App\Models\MediaCategory;
use App\Models\MediaItem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Prop;
use Livewire\Component;

new class extends Component {
    #[Prop]
    public string $fieldKey = '';

    public ?int $selectedCategoryId = null;

    public ?int $selectedImageId = null;

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

    public function selectCategory(?int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->selectedImageId = null;
        unset($this->images);
    }

    public function pickImage(int $id, string $path): void
    {
        $this->dispatch('media-image-picked', key: $this->fieldKey, path: $path);
    }
}; ?>

<div class="flex h-[480px]">
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
    </div>

    {{-- Image grid --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <div class="flex-1 overflow-y-auto p-3">
            @if ($this->images->isEmpty())
                <div class="flex flex-col items-center justify-center h-full py-12 text-zinc-400 dark:text-zinc-600">
                    <flux:icon name="photo" class="size-10 mb-2 opacity-40" />
                    <p class="text-sm">No images here yet.</p>
                </div>
            @else
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach ($this->images as $image)
                        <button
                            wire:key="picker-img-{{ $image->id }}"
                            wire:click="$set('selectedImageId', {{ $image->id }})"
                            class="group relative rounded-lg overflow-hidden border transition-all aspect-square {{ $selectedImageId === $image->id ? 'border-blue-500 ring-2 ring-blue-500' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500' }}"
                        >
                            <img
                                src="{{ $image->url() }}"
                                alt="{{ $image->alt }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                            @if ($image->alt)
                                <div class="absolute bottom-0 inset-x-0 bg-zinc-900/60 px-1.5 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <p class="text-xs text-white truncate">{{ $image->alt }}</p>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Footer with action --}}
        <div class="border-t border-zinc-200 dark:border-zinc-700 px-4 py-3 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 shrink-0">
            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($selectedImageId)
                    @php $selectedImage = $this->images->firstWhere('id', $selectedImageId); @endphp
                    {{ $selectedImage?->filename }}
                @else
                    Click an image to select it
                @endif
            </span>
            <flux:button
                wire:click="pickImage({{ $selectedImageId ?? 'null' }}, '{{ $selectedImageId ? $this->images->firstWhere('id', $selectedImageId)?->path : '' }}')"
                variant="primary"
                size="sm"
                :disabled="! $selectedImageId"
            >
                Use This Image
            </flux:button>
        </div>
    </div>
</div>

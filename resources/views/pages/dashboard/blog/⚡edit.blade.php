<?php

use App\Models\Category;
use App\Models\Post;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Edit Post')] class extends Component {
    use WithFileUploads;

    public Post $post;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $status = 'draft';

    #[Validate('nullable|integer|exists:categories,id')]
    public ?int $categoryId = null;

    #[Validate('nullable|image|max:51200')]
    public $featuredImage = null;

    public string $newCategoryName = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->status = $post->status;
        $this->categoryId = $post->category_id;
    }

    public function createCategory(): void
    {
        $this->validate(['newCategoryName' => 'required|string|max:255|unique:categories,name']);

        $category = Category::create(['name' => $this->newCategoryName]);

        $this->newCategoryName = '';
        $this->categoryId = $category->id;
        $this->dispatch('category-created');
    }

    public function removeFeaturedImage(): void
    {
        if ($this->post->featured_image) {
            Storage::disk('public')->delete($this->post->featured_image);
            $this->post->update(['featured_image' => null]);
        }
    }

    private function performSave(): void
    {
        $this->validate();

        $wasLive = in_array($this->post->status, ['published', 'unlisted']);
        $isGoingLive = in_array($this->status, ['published', 'unlisted']);

        $imagePath = $this->post->featured_image;

        if ($this->featuredImage) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->featuredImage->store('posts', 'public');
            ImageResizer::resizeToMaxWidth($imagePath);
        }

        $this->post->update([
            'title' => $this->title,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'status' => $this->status,
            'category_id' => $this->categoryId,
            'featured_image' => $imagePath,
            'published_at' => $isGoingLive && ! $wasLive ? now() : $this->post->published_at,
        ]);
    }

    public function save(): void
    {
        $this->performSave();
        $this->dispatch('notify', message: 'Post saved.');
    }

    public function saveAndExit(): void
    {
        $this->performSave();
        $this->redirect(route('dashboard.blog.index'), navigate: true);
    }

    public function saveAndView(): void
    {
        $this->performSave();
        $this->redirect(route('blog.show', $this->post->slug));
    }

    public function saveAndAddNew(): void
    {
        $this->performSave();
        $this->redirect(route('dashboard.blog.create'), navigate: true);
    }

    public function saveAndNext(): void
    {
        $this->performSave();

        $nextPost = Post::query()
            ->where('id', '>', $this->post->id)
            ->orderBy('id')
            ->first();

        if ($nextPost) {
            $this->redirect(route('dashboard.blog.edit', $nextPost), navigate: true);
        } else {
            $this->redirect(route('dashboard.blog.index'), navigate: true);
        }
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Category> */
    public function getCategoriesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()->orderBy('name')->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.blog.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Post</flux:heading>
        </div>

        <form wire:submit="save">
            <div class="grid lg:grid-cols-[1fr_288px] gap-8 items-start">

                {{-- Left: main content --}}
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>Title</flux:label>
                        <flux:input wire:model="title" type="text" placeholder="Post title…" autofocus required />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            Excerpt
                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model="excerpt" type="text" placeholder="A brief summary of this post…" />
                        <flux:error name="excerpt" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Content</flux:label>
                        <flux:textarea wire:model="content" rows="20" placeholder="Write your post content here…" />
                        <flux:error name="content" />
                    </flux:field>
                </div>

                {{-- Right: sidebar --}}
                <div class="space-y-5 lg:sticky lg:top-4">

                    {{-- Featured image --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-3"
                        x-data="{
                            preview: null,
                            uploading: false,
                            clearPreview() {
                                this.preview = null;
                                $wire.set('featuredImage', null);
                                this.$refs.fileInput.value = '';
                            },
                            handleFile(event) {
                                const file = event.target.files[0];
                                if (!file) return;

                                const maxWidth = 1920;
                                const reader = new FileReader();

                                reader.onload = (e) => {
                                    const img = new Image();
                                    img.onload = () => {
                                        if (img.width <= maxWidth) {
                                            this.preview = e.target.result;
                                            this.uploading = true;
                                            $wire.upload('featuredImage', file, () => { this.uploading = false; });
                                            return;
                                        }

                                        const scale = maxWidth / img.width;
                                        const canvas = document.createElement('canvas');
                                        canvas.width = maxWidth;
                                        canvas.height = Math.round(img.height * scale);
                                        canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);

                                        this.preview = canvas.toDataURL(file.type, 0.90);
                                        this.uploading = true;

                                        canvas.toBlob((blob) => {
                                            $wire.upload(
                                                'featuredImage',
                                                new File([blob], file.name, { type: blob.type }),
                                                () => { this.uploading = false; }
                                            );
                                        }, file.type, 0.90);
                                    };
                                    img.src = e.target.result;
                                };

                                reader.readAsDataURL(file);
                            }
                        }"
                    >
                        <flux:label>
                            Featured Image
                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                        </flux:label>

                        {{-- New image preview (Alpine-driven) --}}
                        <div x-show="preview" class="relative" x-cloak>
                            <img :src="preview" alt="Preview" class="h-36 w-full object-cover rounded-md" />
                            <div x-show="uploading" class="absolute inset-0 bg-black/40 flex items-center justify-center rounded-md">
                                <span class="text-white text-sm font-medium">Uploading…</span>
                            </div>
                            <button
                                type="button"
                                x-show="!uploading"
                                @click="clearPreview()"
                                class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded hover:bg-black/80 transition-colors"
                            >
                                Remove
                            </button>
                        </div>

                        {{-- Existing image (shown when no new upload in progress) --}}
                        @if ($post->featured_image)
                            <div x-show="!preview" class="relative group">
                                <img src="{{ $post->featuredImageUrl() }}" alt="Current featured image" class="h-36 w-full object-cover rounded-md" />
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 rounded-md">
                                    <flux:button
                                        type="button"
                                        wire:click="removeFeaturedImage"
                                        wire:confirm="Remove the featured image?"
                                        variant="danger"
                                        size="sm"
                                    >
                                        Remove
                                    </flux:button>
                                </div>
                            </div>
                        @endif

                        <input
                            type="file"
                            x-ref="fileInput"
                            @change="handleFile($event)"
                            accept="image/*"
                            class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-zinc-300 dark:file:border-zinc-600 file:text-sm file:font-medium file:bg-zinc-50 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-100 dark:hover:file:bg-zinc-700 transition-colors"
                        />
                        <flux:error name="featuredImage" />
                    </div>

                    {{-- Status & Category --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-4">
                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="draft">Draft</flux:select.option>
                                <flux:select.option value="published">Published</flux:select.option>
                                <flux:select.option value="unlisted">Unlisted</flux:select.option>
                                <flux:select.option value="unpublished">Unpublished</flux:select.option>
                            </flux:select>
                            <div x-data>
                                <flux:description x-show="$wire.status === 'draft'">Saved but not yet visible to the public.</flux:description>
                                <flux:description x-show="$wire.status === 'published'">Live and visible in listings and search.</flux:description>
                                <flux:description x-show="$wire.status === 'unlisted'">Accessible via direct link, but hidden from listings and search.</flux:description>
                                <flux:description x-show="$wire.status === 'unpublished'">Removed from public access — visitors will see a 404.</flux:description>
                            </div>
                            <flux:error name="status" />
                        </flux:field>

                        <div x-data="{ adding: false }">
                            <flux:field>
                                <div class="flex items-center justify-between mb-2">
                                    <flux:label class="mb-0">
                                        Category
                                        <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                    </flux:label>
                                    <button
                                        type="button"
                                        x-show="!adding"
                                        @click="adding = true"
                                        class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors"
                                    >+ New</button>
                                </div>

                                <flux:select wire:model="categoryId" x-show="!adding">
                                    <flux:select.option value="">No category</flux:select.option>
                                    @foreach ($this->categories as $category)
                                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="categoryId" />
                            </flux:field>

                            <div x-show="adding" x-cloak class="mt-2 space-y-2">
                                <flux:input
                                    wire:model="newCategoryName"
                                    type="text"
                                    placeholder="Category name…"
                                    x-on:category-created.window="adding = false"
                                />
                                <div class="flex gap-2">
                                    <flux:button type="button" wire:click="createCategory" variant="primary" size="sm">
                                        Create
                                    </flux:button>
                                    <flux:button type="button" @click="adding = false; $wire.set('newCategoryName', '')" variant="ghost" size="sm">
                                        Cancel
                                    </flux:button>
                                </div>
                            </div>
                            <flux:error name="newCategoryName" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-2">
                        <flux:button.group class="w-full">
                            <flux:button
                                type="submit"
                                variant="primary"
                                class="flex-1 justify-center"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                Update Post
                            </flux:button>
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="primary" icon="chevron-down" wire:loading.attr="disabled" />
                                <flux:menu>
                                    <flux:menu.item wire:click="saveAndExit" icon="arrow-left">Save + Exit</flux:menu.item>
                                    <flux:menu.item wire:click="saveAndView" icon="arrow-top-right-on-square">Save + View</flux:menu.item>
                                    <flux:menu.item wire:click="saveAndAddNew" icon="document-plus">Save + Add New</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="saveAndNext" icon="chevron-right">Save + Next</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:button.group>
                        <flux:button href="{{ route('dashboard.blog.index') }}" variant="ghost" wire:navigate class="w-full justify-center">
                            Cancel
                        </flux:button>
                    </div>

                </div>
            </div>
        </form>
    </flux:main>
</div>

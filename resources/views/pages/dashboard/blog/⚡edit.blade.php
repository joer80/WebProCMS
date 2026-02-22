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

    #[Validate('required|in:image-top,image-right')]
    public string $layout = 'image-top';

    #[Validate('nullable|integer|exists:categories,id')]
    public ?int $categoryId = null;

    #[Validate('nullable|image|max:51200')]
    public $featuredImage = null;

    #[Validate([
        'ctaButtons.*.text' => 'nullable|string|max:255',
        'ctaButtons.*.url' => 'nullable|url|max:2048',
        'ctaButtons.*.newTab' => 'nullable|boolean',
    ])]
    public array $ctaButtons = [];

    public string $newCategoryName = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->status = $post->status;
        $this->layout = $post->layout ?? 'image-top';
        $this->categoryId = $post->category_id;
        $this->ctaButtons = array_map(fn ($btn) => [
            'text' => $btn['text'] ?? '',
            'url' => $btn['url'] ?? '',
            'newTab' => ($btn['target'] ?? '_self') === '_blank',
        ], $post->cta_buttons ?? []);
    }

    public function addCtaButton(): void
    {
        if (count($this->ctaButtons) < 2) {
            $this->ctaButtons[] = ['text' => '', 'url' => '', 'newTab' => false];
        }
    }

    public function removeCtaButton(int $index): void
    {
        array_splice($this->ctaButtons, $index, 1);
        $this->ctaButtons = array_values($this->ctaButtons);
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

        $ctaButtons = array_values(array_filter(
            array_map(fn ($btn) => [
                'text' => trim($btn['text'] ?? ''),
                'url' => trim($btn['url'] ?? ''),
                'target' => ($btn['newTab'] ?? false) ? '_blank' : '_self',
            ], $this->ctaButtons),
            fn ($btn) => $btn['text'] !== '' && $btn['url'] !== ''
        ));

        $this->post->update([
            'title' => $this->title,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'cta_buttons' => $ctaButtons ?: null,
            'status' => $this->status,
            'layout' => $this->layout,
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

                    {{-- Call to Action Buttons --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:label class="mb-0">
                                    Call to Action Buttons
                                    <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                </flux:label>
                                <flux:description class="mt-0.5">Displayed below the post content.</flux:description>
                            </div>
                            @if (count($ctaButtons) < 2)
                                <flux:button type="button" wire:click="addCtaButton" size="sm" variant="ghost" icon="plus">Add Button</flux:button>
                            @endif
                        </div>

                        @if (count($ctaButtons) > 0)
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($ctaButtons as $index => $button)
                                    <div class="space-y-3 p-3 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700" wire:key="cta-{{ $index }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Button {{ $index + 1 }}</span>
                                            <button
                                                type="button"
                                                wire:click="removeCtaButton({{ $index }})"
                                                class="text-xs text-zinc-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                            >Remove</button>
                                        </div>

                                        <flux:field>
                                            <flux:label>Button Text</flux:label>
                                            <flux:input wire:model="ctaButtons.{{ $index }}.text" type="text" placeholder="Get Started" />
                                            <flux:error name="ctaButtons.{{ $index }}.text" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Button URL</flux:label>
                                            <flux:input wire:model="ctaButtons.{{ $index }}.url" type="url" placeholder="https://…" />
                                            <flux:error name="ctaButtons.{{ $index }}.url" />
                                        </flux:field>

                                        <flux:switch wire:model="ctaButtons.{{ $index }}.newTab" label="Open in new tab" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-center text-zinc-400 dark:text-zinc-500 py-1">No buttons added yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Right: sidebar --}}
                <div class="space-y-5 lg:sticky lg:top-4">

                    {{-- Actions --}}
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
                                <flux:menu.separator />
                                <flux:menu.item :href="route('dashboard.blog.index')" wire:navigate icon="x-mark">Cancel</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:button.group>

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

                    {{-- Layout --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-3">
                        <flux:label>Layout</flux:label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="layout" value="image-top" class="sr-only peer" />
                                <div class="rounded-md border-2 border-zinc-200 dark:border-zinc-700 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 p-2 transition-colors">
                                    {{-- Mini preview: image-top --}}
                                    <div class="space-y-1 mb-2">
                                        <div class="h-6 w-full bg-zinc-300 dark:bg-zinc-600 rounded-sm"></div>
                                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                        <div class="h-1.5 w-4/5 bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                    </div>
                                    <p class="text-xs text-center text-zinc-600 dark:text-zinc-400 font-medium">Image Top</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="layout" value="image-right" class="sr-only peer" />
                                <div class="rounded-md border-2 border-zinc-200 dark:border-zinc-700 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 p-2 transition-colors">
                                    {{-- Mini preview: image-right --}}
                                    <div class="flex gap-1 mb-2">
                                        <div class="flex-1 space-y-1">
                                            <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                            <div class="h-1.5 w-4/5 bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                            <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                            <div class="h-1.5 w-3/4 bg-zinc-200 dark:bg-zinc-700 rounded-sm"></div>
                                        </div>
                                        <div class="w-8 h-full bg-zinc-300 dark:bg-zinc-600 rounded-sm self-stretch min-h-8"></div>
                                    </div>
                                    <p class="text-xs text-center text-zinc-600 dark:text-zinc-400 font-medium">Image Right</p>
                                </div>
                            </label>
                        </div>
                        <flux:error name="layout" />
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


                </div>
            </div>
        </form>
    </flux:main>
</div>

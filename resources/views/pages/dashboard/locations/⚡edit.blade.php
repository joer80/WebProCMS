<?php

use App\Models\Location;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Edit Location')] class extends Component {
    use WithFileUploads;

    public Location $location;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $address = '';

    #[Validate]
    public string $city = '';

    #[Validate]
    public string $state = '';

    #[Validate]
    public string $zip = '';

    #[Validate]
    public string $phone = '';

    #[Validate('nullable|image|max:51200')]
    public $photo = null;

    public function mount(Location $location): void
    {
        $this->location = $location;
        $this->name = $location->name;
        $this->address = $location->address;
        $this->city = $location->city;
        $this->state = $location->state;
        $this->zip = $location->zip;
        $this->phone = $location->phone;
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:51200'],
        ];
    }

    public function removePhoto(): void
    {
        if ($this->location->photo) {
            Storage::disk('public')->delete($this->location->photo);
            $this->location->update(['photo' => null]);
        }
    }

    public function save(): void
    {
        $this->validate();

        $photoPath = $this->location->photo;

        if ($this->photo) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $this->photo->store('locations', 'public');
            ImageResizer::resizeToMaxWidth($photoPath);
        }

        $this->location->update([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => strtoupper($this->state),
            'zip' => $this->zip,
            'phone' => $this->phone,
            'photo' => $photoPath,
        ]);

        $this->redirect(route('dashboard.locations.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.locations.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Location</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-lg space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Address</flux:label>
                <flux:input wire:model="address" type="text" required />
                <flux:error name="address" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model="city" type="text" required />
                    <flux:error name="city" />
                </flux:field>

                <flux:field>
                    <flux:label>State</flux:label>
                    <flux:input wire:model="state" type="text" maxlength="2" required />
                    <flux:error name="state" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>ZIP Code</flux:label>
                    <flux:input wire:model="zip" type="text" required />
                    <flux:error name="zip" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input wire:model="phone" type="text" required />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <div
                x-data="{
                    preview: null,
                    uploading: false,
                    clearPreview() {
                        this.preview = null;
                        $wire.set('photo', null);
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
                                    $wire.upload('photo', file, () => { this.uploading = false; });
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
                                        'photo',
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
                    Photo
                    <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                </flux:label>

                {{-- New upload preview (Alpine-driven) --}}
                <div x-show="preview" class="relative mt-2 mb-3" x-cloak>
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

                {{-- Existing photo (shown when no new upload in progress) --}}
                @if ($location->photo)
                    <div x-show="!preview" class="relative group mt-2 mb-3">
                        <img src="{{ $location->photoUrl() }}" alt="Current photo" class="h-36 w-full object-cover rounded-md" />
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 rounded-md">
                            <flux:button
                                type="button"
                                wire:click="removePhoto"
                                wire:confirm="Remove the location photo?"
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
                    class="mt-2 block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-zinc-300 dark:file:border-zinc-600 file:text-sm file:font-medium file:bg-zinc-50 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-100 dark:hover:file:bg-zinc-700 transition-colors"
                />
                <flux:error name="photo" />
            </div>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Location</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.locations.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

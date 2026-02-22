<?php

use App\Models\Location;
use App\Support\ImageResizer;
use App\Support\States;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('New Location')] class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $address = '';

    #[Validate('required|string|max:255')]
    public string $city = '';

    #[Validate('required|string|size:2')]
    public string $state = '';

    public string $state_full = '';

    #[Validate('required|string|max:10')]
    public string $zip = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|image|max:51200')]
    public $photo = null;

    public function updatedState(string $value): void
    {
        if (strlen($value) === 2) {
            $this->state_full = States::fullName(strtoupper($value));
        }
    }

    public function save(): void
    {
        $this->validate();

        $photoPath = null;

        if ($this->photo) {
            $photoPath = $this->photo->store('locations', 'public');
            ImageResizer::resizeToMaxWidth($photoPath);
        }

        Location::create([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => strtoupper($this->state),
            'state_full' => $this->state_full,
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
            <flux:heading size="xl">New Location</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-lg space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" placeholder="e.g. GetRows Nashville" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Address</flux:label>
                <flux:input wire:model="address" type="text" placeholder="e.g. 123 Main Street" required />
                <flux:error name="address" />
            </flux:field>

            <div class="grid grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model="city" type="text" placeholder="e.g. Nashville" required />
                    <flux:error name="city" />
                </flux:field>

                <flux:field>
                    <flux:label>State</flux:label>
                    <flux:select wire:model.live="state" required>
                        <option value="">Select a state</option>
                        @foreach(\App\Support\States::all() as $abbr => $name)
                            <option value="{{ $abbr }}">{{ $abbr }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="state" />
                </flux:field>

                <div class="opacity-50">
                    <flux:field>
                        <flux:label>State (Full Name)</flux:label>
                        <flux:input wire:model="state_full" type="text" readonly />
                    </flux:field>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>ZIP Code</flux:label>
                    <flux:input wire:model="zip" type="text" placeholder="e.g. 37201" required />
                    <flux:error name="zip" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input wire:model="phone" type="text" placeholder="e.g. (615) 555-0101" required />
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
                    <span wire:loading.remove>Save Location</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.locations.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

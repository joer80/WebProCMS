<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] #[Title('Edit Category')] class extends Component {
    public Category $category;

    #[Validate]
    public string $name = '';

    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->category->id)],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->category->update(['name' => $this->name]);

        $this->redirect(route('dashboard.categories.index'), navigate: true);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.categories.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Category</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-sm space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" autofocus required />
                <flux:error name="name" />
            </flux:field>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Category</span>
                    <span wire:loading>Saving…</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.categories.index') }}" variant="ghost" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:main>
</div>

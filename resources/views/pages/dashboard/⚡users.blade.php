<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Users')] #[Lazy] class extends Component {
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="flex items-center justify-center py-32">
            <svg class="animate-spin size-8 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        HTML;
    }

    public bool $showModal = false;
    public ?int $editingUserId = null;
    public ?int $confirmingDelete = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'standard';

    /** @return \Illuminate\Database\Eloquent\Collection<int, User> */
    #[Computed]
    public function users(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user) => $this->currentUser()->isAtLeast($user->role))
            ->values();
    }

    #[Computed]
    public function currentUser(): User
    {
        return Auth::user();
    }

    /** @return array<string, string> */
    #[Computed]
    public function availableRoles(): array
    {
        return collect(Role::cases())
            ->filter(fn (Role $role) => $this->currentUser()->isAtLeast($role))
            ->mapWithKeys(fn (Role $role) => [strtolower($role->name) => ucfirst(strtolower($role->name))])
            ->all();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->guardAgainst($user);

        $this->editingUserId = $userId;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = strtolower($user->role->name);
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                $this->editingUserId === null
                    ? Rule::unique(User::class)
                    : Rule::unique(User::class)->ignore($this->editingUserId),
            ],
            'role' => ['required', Rule::in(array_keys($this->availableRoles))],
        ];

        if ($this->editingUserId === null) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } elseif ($this->password !== '') {
            $rules['password'] = ['string', 'min:8'];
        }

        $validated = $this->validate($rules);

        if ($this->editingUserId === null) {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
            ]);

            $message = 'User created.';
        } else {
            $user = User::query()->findOrFail($this->editingUserId);
            $this->guardAgainst($user);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            if ($this->password !== '') {
                $data['password'] = $this->password;
            }

            $user->update($data);

            $message = 'User updated.';
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->users);

        $this->dispatch('notify', message: $message);
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Auth::id()) {
            return;
        }

        $user = User::query()->findOrFail($userId);
        $this->guardAgainst($user);

        $user->delete();
        $this->confirmingDelete = null;
        unset($this->users);

        $this->dispatch('notify', message: 'User deleted.');
    }

    private function guardAgainst(User $target): void
    {
        if (! $this->currentUser()->isAtLeast($target->role)) {
            abort(403);
        }
    }

    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'standard';
    }
}; ?>

<div>
    <flux:modal wire:model="showModal" class="max-w-md w-full">
        <flux:heading size="lg" class="mb-6">
            {{ $editingUserId ? __('Edit User') : __('New User') }}
        </flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
            />

            <flux:input
                wire:model="email"
                :label="__('Email')"
                type="email"
                required
            />

            <flux:input
                wire:model="password"
                :label="$editingUserId ? __('New Password (leave blank to keep)') : __('Password')"
                type="password"
                autocomplete="new-password"
                :required="$editingUserId === null"
            />

            <flux:select wire:model="role" :label="__('Role')">
                @foreach ($this->availableRoles as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingUserId ? __('Update User') : __('Create User') }}</span>
                    <span wire:loading>{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">{{ __('Users') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Manage user accounts and roles.') }}</flux:text>
            </div>
            <flux:button wire:click="openCreateModal" variant="primary">
                {{ __('New User') }}
            </flux:button>
        </div>

        @if ($this->users->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="users" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">{{ __('No users found.') }}</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">{{ __('Name') }}</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">{{ __('Email') }}</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">{{ __('Role') }}</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">{{ __('Joined') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $user->name }}
                                    @if ($user->id === Auth::id())
                                        <flux:badge size="sm" class="ml-1">{{ __('You') }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $roleColor = match ($user->role) {
                                            \App\Enums\Role::Super   => 'red',
                                            \App\Enums\Role::Admin   => 'blue',
                                            \App\Enums\Role::Manager => 'lime',
                                            default                  => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge color="{{ $roleColor }}" size="sm">
                                        {{ ucfirst(strtolower($user->role->name)) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden md:table-cell">
                                    {{ $user->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            wire:click="openEditModal({{ $user->id }})"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                        />
                                        @if ($user->id !== Auth::id())
                                            @if ($confirmingDelete === $user->id)
                                                <div class="flex items-center gap-1">
                                                    <flux:button wire:click="deleteUser({{ $user->id }})" variant="danger" size="sm">
                                                        {{ __('Confirm') }}
                                                    </flux:button>
                                                    <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                        {{ __('Cancel') }}
                                                    </flux:button>
                                                </div>
                                            @else
                                                <flux:button
                                                    wire:click="$set('confirmingDelete', {{ $user->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="text-red-500 dark:text-red-400"
                                                />
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:main>
</div>

<?php

use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Locations')] class extends Component {
    public ?int $confirmingDelete = null;

    public function deleteLocation(int $locationId): void
    {
        Location::query()->findOrFail($locationId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Location> */
    public function getLocationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Location::query()
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Locations</flux:heading>
                <flux:text class="mt-1">Manage your GetRows store locations.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.locations.create') }}" variant="primary" wire:navigate>
                New Location
            </flux:button>
        </div>

        @if ($this->locations->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="map-pin" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No locations yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">City</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">State</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Phone</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->locations as $location)
                            <tr wire:key="location-{{ $location->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $location->name }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden md:table-cell">
                                    {{ $location->city }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">
                                    {{ $location->state }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                                    {{ $location->phone }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            href="{{ route('dashboard.locations.edit', $location) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        @if ($confirmingDelete === $location->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteLocation({{ $location->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', {{ $location->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="text-red-500 dark:text-red-400"
                                            />
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

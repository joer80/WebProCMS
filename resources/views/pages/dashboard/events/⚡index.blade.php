<?php

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Events')] #[Lazy] class extends Component {
    use WithPagination;

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

    public ?int $confirmingDelete = null;

    public function deleteEvent(int $eventId): void
    {
        $event = Event::query()->findOrFail($eventId);
        $event->childEvents()->delete();
        $event->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Event> */
    public function getEventsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Event::query()
            ->orderBy('start_date', 'asc')
            ->paginate(15);
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Events</flux:heading>
                <flux:text class="mt-1">Manage your events.</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('dashboard.events.create') }}" variant="primary" wire:navigate>
                    New Event
                </flux:button>
            </div>
        </div>

        @if ($this->events->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="calendar-days" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No events yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Title</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Start Date</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden xl:table-cell">End Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->events as $event)
                            <tr wire:key="event-{{ $event->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $event->title }}</span>
                                        @if ($event->parent_event_id)
                                            <flux:badge size="sm" variant="outline">Child</flux:badge>
                                        @endif
                                    </div>
                                    @if ($event->excerpt)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-xs mt-0.5">{{ $event->excerpt }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    @php
                                        $badgeVariant = match($event->status) {
                                            'published' => 'green',
                                            'unlisted' => 'blue',
                                            'unpublished' => 'red',
                                            default => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge variant="{{ $badgeVariant }}" size="sm">
                                        {{ ucfirst($event->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden lg:table-cell">
                                    @if ($event->is_all_day)
                                        {{ $event->start_date->format('M j, Y') }}
                                    @else
                                        {{ $event->start_date->format('M j, Y g:i A') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden xl:table-cell">
                                    @if ($event->end_date)
                                        @if ($event->is_all_day)
                                            {{ $event->end_date->format('M j, Y') }}
                                        @else
                                            {{ $event->end_date->format('M j, Y g:i A') }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:tooltip content="Edit event" position="bottom">
                                            <flux:button
                                                href="{{ route('dashboard.events.edit', $event) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil"
                                                wire:navigate
                                            />
                                        </flux:tooltip>
                                        @if (in_array($event->status, ['published', 'unlisted']))
                                            <flux:tooltip content="View event" position="bottom">
                                                <flux:button
                                                    href="{{ route('events.show', $event->slug) }}"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="arrow-top-right-on-square"
                                                    target="_blank"
                                                />
                                            </flux:tooltip>
                                        @endif
                                        @if ($confirmingDelete === $event->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteEvent({{ $event->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:tooltip content="Delete event" position="bottom">
                                                <flux:button
                                                    wire:click="$set('confirmingDelete', {{ $event->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="text-red-500 dark:text-red-400"
                                                />
                                            </flux:tooltip>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $this->events->links() }}
            </div>
        @endif
    </flux:main>
</div>

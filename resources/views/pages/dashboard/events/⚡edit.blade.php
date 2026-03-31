<?php

use App\Models\Event;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Edit Event')] class extends Component {
    use WithFileUploads;

    public Event $event;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('nullable|string')]
    public string $content = '';

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $status = 'draft';

    #[Validate('required|in:image-top,image-right')]
    public string $layout = 'image-top';

    #[Validate('nullable|image|max:51200')]
    public $featuredImage = null;

    #[Validate('nullable|string|max:255')]
    public string $featuredImageAlt = '';

    #[Validate([
        'ctaButtons.*.text' => 'nullable|string|max:255',
        'ctaButtons.*.url' => 'nullable|url|max:2048',
        'ctaButtons.*.newTab' => 'nullable|boolean',
    ])]
    public array $ctaButtons = [];

    #[Validate('nullable|image|max:51200')]
    public $newGalleryImage = null;

    #[Validate([
        'galleryImages.*.path' => 'nullable|string',
        'galleryImages.*.alt' => 'nullable|string|max:255',
    ])]
    public array $galleryImages = [];

    #[Validate('required|integer|in:2,3,4,5')]
    public int $galleryColumns = 4;

    #[Validate('nullable|string|max:255')]
    public string $metaTitle = '';

    #[Validate('nullable|string|max:320')]
    public string $metaDescription = '';

    #[Validate('nullable|boolean')]
    public bool $isNoindex = false;

    #[Validate('nullable|url|max:2048')]
    public string $ogImage = '';

    #[Validate('required|date')]
    public string $startDate = '';

    #[Validate('nullable|date')]
    public string $endDate = '';

    #[Validate('nullable|boolean')]
    public bool $isAllDay = false;

    #[Validate('nullable|string|max:255')]
    public string $eventTimezone = '';

    #[Validate('nullable|string|max:255')]
    public string $venueName = '';

    #[Validate('nullable|string|max:1000')]
    public string $venueAddress = '';

    #[Validate('nullable|url|max:2048')]
    public string $websiteUrl = '';

    #[Validate('nullable|string|max:255')]
    public string $cost = '';

    #[Validate('nullable|boolean')]
    public bool $isRepeating = false;

    #[Validate('nullable|in:daily,weekly,monthly,yearly')]
    public string $repeatFrequency = 'weekly';

    #[Validate('nullable|integer|min:1|max:365')]
    public int $repeatInterval = 1;

    #[Validate('nullable|date')]
    public string $repeatEndsAt = '';

    #[Validate('nullable|array')]
    public array $repeatDays = [];

    public string $repeatEndsOption = 'never';

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->title = $event->title;
        $this->excerpt = $event->excerpt ?? '';
        $this->content = $event->content ?? '';
        $this->status = $event->status;
        $this->layout = $event->layout ?? 'image-top';
        $this->featuredImageAlt = $event->featured_image_alt ?? '';
        $this->ctaButtons = array_map(fn ($btn) => [
            'text' => $btn['text'] ?? '',
            'url' => $btn['url'] ?? '',
            'newTab' => ($btn['target'] ?? '_self') === '_blank',
        ], $event->cta_buttons ?? []);

        $this->galleryImages = array_values(array_map(
            fn ($item) => is_string($item) ? ['path' => $item, 'alt' => ''] : ['path' => $item['path'] ?? '', 'alt' => $item['alt'] ?? ''],
            $event->gallery_images ?? []
        ));
        $this->galleryColumns = $event->gallery_columns ?? 4;
        $this->metaTitle = $event->meta_title ?? '';
        $this->metaDescription = $event->meta_description ?? '';
        $this->isNoindex = $event->is_noindex ?? false;
        $this->ogImage = $event->og_image ?? '';

        $this->startDate = $event->start_date?->format('Y-m-d\TH:i') ?? '';
        $this->endDate = $event->end_date?->format('Y-m-d\TH:i') ?? '';
        $this->isAllDay = $event->is_all_day ?? false;
        $this->eventTimezone = $event->timezone ?? \App\Models\Setting::get('site.timezone', date_default_timezone_get());
        $this->venueName = $event->venue_name ?? '';
        $this->venueAddress = $event->venue_address ?? '';
        $this->websiteUrl = $event->website_url ?? '';
        $this->cost = $event->cost ?? '';
        $this->isRepeating = $event->is_repeating ?? false;
        $this->repeatFrequency = $event->repeat_frequency ?? 'weekly';
        $this->repeatInterval = $event->repeat_interval ?? 1;
        $this->repeatEndsAt = $event->repeat_ends_at?->format('Y-m-d') ?? '';
        $this->repeatDays = $event->repeat_days ?? [];
        $this->repeatEndsOption = $event->repeat_ends_at ? 'on' : 'never';
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

    public function addGalleryImage(): void
    {
        $this->validateOnly('newGalleryImage');

        $path = $this->newGalleryImage->store('events', 'public');
        ImageResizer::resizeToMaxWidth($path);

        $this->galleryImages[] = ['path' => $path, 'alt' => ''];
        $this->reset('newGalleryImage');
    }

    public function removeGalleryImage(int $index): void
    {
        $path = $this->galleryImages[$index]['path'] ?? null;

        if ($path) {
            Storage::disk('public')->delete($path);
            array_splice($this->galleryImages, $index, 1);
            $this->galleryImages = array_values($this->galleryImages);
        }
    }

    public function removeFeaturedImage(): void
    {
        if ($this->event->featured_image) {
            Storage::disk('public')->delete($this->event->featured_image);
            $this->event->update(['featured_image' => null]);
        }
    }

    private function performSave(): void
    {
        $this->validate();

        $wasRepeating = $this->event->is_repeating;
        $wasLive = in_array($this->event->status, ['published', 'unlisted']);
        $isGoingLive = in_array($this->status, ['published', 'unlisted']);

        $imagePath = $this->event->featured_image;

        if ($this->featuredImage) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->featuredImage->store('events', 'public');
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

        // If toggling repeating off, delete child events
        if ($wasRepeating && ! $this->isRepeating) {
            $this->event->childEvents()->delete();
        }

        $this->event->update([
            'title' => $this->title,
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content ?: null,
            'cta_buttons' => $ctaButtons ?: null,
            'gallery_images' => ! empty($this->galleryImages) ? $this->galleryImages : null,
            'gallery_columns' => $this->galleryColumns,
            'status' => $this->status,
            'layout' => $this->layout,
            'featured_image' => $imagePath,
            'featured_image_alt' => $this->featuredImageAlt ?: null,
            'published_at' => $isGoingLive && ! $wasLive ? now() : $this->event->published_at,
            'meta_title' => $this->metaTitle ?: null,
            'meta_description' => $this->metaDescription ?: null,
            'is_noindex' => $this->isNoindex,
            'og_image' => $this->ogImage ?: null,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate ?: null,
            'is_all_day' => $this->isAllDay,
            'timezone' => $this->eventTimezone ?: null,
            'venue_name' => $this->venueName ?: null,
            'venue_address' => $this->venueAddress ?: null,
            'website_url' => $this->websiteUrl ?: null,
            'cost' => $this->cost ?: null,
            'is_repeating' => $this->isRepeating,
            'repeat_frequency' => $this->isRepeating ? $this->repeatFrequency : null,
            'repeat_interval' => $this->isRepeating ? $this->repeatInterval : 1,
            'repeat_ends_at' => ($this->isRepeating && $this->repeatEndsOption === 'on') ? ($this->repeatEndsAt ?: null) : null,
            'repeat_days' => ($this->isRepeating && $this->repeatFrequency === 'weekly') ? $this->repeatDays : null,
        ]);
    }

    public function save(): void
    {
        $this->performSave();
        $this->dispatch('notify', message: 'Event saved.');
    }

    public function saveAndExit(): void
    {
        $this->performSave();
        $this->redirect(route('dashboard.events.index'), navigate: true);
    }

    public function saveAndView(): void
    {
        $this->performSave();
        $this->redirect(route('events.show', $this->event->slug));
    }

    public function saveAndAddNew(): void
    {
        $this->performSave();
        $this->redirect(route('dashboard.events.create'), navigate: true);
    }

    public function saveAndNext(): void
    {
        $this->performSave();

        $nextEvent = Event::query()
            ->where('id', '>', $this->event->id)
            ->orderBy('id')
            ->first();

        if ($nextEvent) {
            $this->redirect(route('dashboard.events.edit', $nextEvent), navigate: true);
        } else {
            $this->redirect(route('dashboard.events.index'), navigate: true);
        }
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8 flex items-center gap-4">
            <flux:button href="{{ route('dashboard.events.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Edit Event</flux:heading>
        </div>

        <form wire:submit="save">
            <div class="grid lg:grid-cols-[1fr_288px] gap-8 items-start">

                {{-- Left: main content --}}
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>Title</flux:label>
                        <flux:input wire:model="title" type="text" placeholder="Event title…" autofocus required />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            Excerpt
                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model="excerpt" type="text" placeholder="A brief summary of this event…" />
                        <flux:error name="excerpt" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            Content
                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                        </flux:label>
                        <div
                            wire:ignore
                            x-data="richEditor(@js($content))"
                            class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                        >
                            {{-- Toolbar --}}
                            <div class="flex flex-wrap items-center gap-px border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-1.5">
                                <select
                                    :value="headingLevel"
                                    @change="setHeading($event.target.value)"
                                    class="h-7 rounded border-0 bg-transparent text-xs text-zinc-700 dark:text-zinc-300 focus:ring-0 cursor-pointer pr-6"
                                >
                                    <option value="0">Normal</option>
                                    <option value="1">Heading 1</option>
                                    <option value="2">Heading 2</option>
                                    <option value="3">Heading 3</option>
                                </select>

                                <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                <button type="button" @click="cmd().toggleBold().run()" :class="active.bold ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">B</button>
                                <button type="button" @click="cmd().toggleItalic().run()" :class="active.italic ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm italic text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">I</button>
                                <button type="button" @click="cmd().toggleUnderline().run()" :class="active.underline ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm underline text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">U</button>
                                <button type="button" @click="cmd().toggleStrike().run()" :class="active.strike ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 w-7 items-center justify-center rounded text-sm line-through text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">S</button>

                                <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                <button type="button" @click="cmd().toggleBlockquote().run()" :class="active.blockquote ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 font-mono">&ldquo;</button>
                                <button type="button" @click="cmd().toggleCodeBlock().run()" :class="active.codeBlock ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 font-mono">&lt;/&gt;</button>

                                <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                <button type="button" @click="cmd().toggleBulletList().run()" :class="active.bulletList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">• List</button>
                                <button type="button" @click="cmd().toggleOrderedList().run()" :class="active.orderedList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">1. List</button>

                                <div class="mx-1 h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

                                <button type="button" @click="setLink()" :class="active.link ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600">Link</button>
                                <button type="button" @click="cmd().unsetAllMarks().clearNodes().run()" class="flex h-7 items-center justify-center rounded px-2 text-xs text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-600">Clear</button>
                                <button type="button" @click="toggleSource()" :class="sourceMode ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="ml-auto flex h-7 items-center justify-center rounded px-2 text-xs font-mono text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-600">&lt;/&gt;</button>
                            </div>

                            {{-- Editor --}}
                            <div x-ref="editorEl" class="min-h-80" x-show="!sourceMode"></div>
                            <textarea x-show="sourceMode" x-model="sourceHtml" class="w-full min-h-80 p-4 font-mono text-sm text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-900 outline-none resize-y border-0"></textarea>
                        </div>
                        <flux:error name="content" />
                    </flux:field>

                    {{-- Event Details --}}
                    <div
                        x-data="{ open: true }"
                        class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between p-4 text-left"
                        >
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Event Details</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="px-4 pb-4">
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">

                                {{-- Time & Date --}}
                                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Time & Date</p>

                                <div class="grid grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>Start Date & Time</flux:label>
                                        <flux:input wire:model="startDate" type="datetime-local" />
                                        <flux:error name="startDate" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>
                                            End Date & Time
                                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                        </flux:label>
                                        <flux:input wire:model="endDate" type="datetime-local" />
                                        <flux:error name="endDate" />
                                    </flux:field>
                                </div>

                                <flux:switch wire:model="isAllDay" label="All Day Event" />

                                <flux:field>
                                    <flux:label>Timezone</flux:label>
                                    <flux:select wire:model="eventTimezone">
                                        @php
                                            $tzRegions = [];
                                            foreach (\DateTimeZone::listIdentifiers() as $tz) {
                                                $parts = explode('/', $tz, 2);
                                                $region = $parts[0];
                                                $tzRegions[$region][] = $tz;
                                            }
                                        @endphp
                                        @foreach ($tzRegions as $region => $timezones)
                                            <optgroup label="{{ $region }}">
                                                @foreach ($timezones as $tz)
                                                    <option value="{{ $tz }}" @selected($eventTimezone === $tz)>{{ $tz }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="eventTimezone" />
                                </flux:field>

                                {{-- Location --}}
                                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-4">Location</p>
                                    <div class="space-y-4">
                                        <flux:field>
                                            <flux:label>
                                                Venue Name
                                                <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                            </flux:label>
                                            <flux:input wire:model="venueName" type="text" placeholder="e.g. Convention Center" />
                                            <flux:error name="venueName" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>
                                                Venue Address
                                                <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                            </flux:label>
                                            <flux:textarea wire:model="venueAddress" rows="3" placeholder="123 Main St, City, State 12345" />
                                            <flux:error name="venueAddress" />
                                        </flux:field>
                                    </div>
                                </div>

                                {{-- Event URL & Cost --}}
                                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                                    <flux:field>
                                        <flux:label>
                                            Event Website URL
                                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                        </flux:label>
                                        <flux:input wire:model="websiteUrl" type="url" placeholder="https://…" />
                                        <flux:error name="websiteUrl" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>
                                            Cost
                                            <flux:badge size="sm" variant="outline" class="ml-1">Optional</flux:badge>
                                        </flux:label>
                                        <flux:input wire:model="cost" type="text" placeholder="Free, $25, $10–$50" />
                                        <flux:error name="cost" />
                                    </flux:field>
                                </div>

                                {{-- Repeating Event --}}
                                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4" x-data="{ repeating: @entangle('isRepeating'), frequency: @entangle('repeatFrequency'), endsOption: @entangle('repeatEndsOption') }">
                                    <flux:switch wire:model.live="isRepeating" label="Repeating Event" x-model="repeating" />

                                    <div x-show="repeating" x-transition class="mt-4 space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <flux:field>
                                                <flux:label>Frequency</flux:label>
                                                <flux:select wire:model.live="repeatFrequency" x-model="frequency">
                                                    <flux:select.option value="daily">Daily</flux:select.option>
                                                    <flux:select.option value="weekly">Weekly</flux:select.option>
                                                    <flux:select.option value="monthly">Monthly</flux:select.option>
                                                    <flux:select.option value="yearly">Yearly</flux:select.option>
                                                </flux:select>
                                                <flux:error name="repeatFrequency" />
                                            </flux:field>
                                            <flux:field>
                                                <flux:label>
                                                    Every
                                                    <span x-text="frequency === 'daily' ? 'day(s)' : frequency === 'weekly' ? 'week(s)' : frequency === 'monthly' ? 'month(s)' : 'year(s)'" class="text-zinc-400 ml-1"></span>
                                                </flux:label>
                                                <flux:input wire:model="repeatInterval" type="number" min="1" max="365" />
                                                <flux:error name="repeatInterval" />
                                            </flux:field>
                                        </div>

                                        {{-- Repeat days (weekly only) --}}
                                        <div x-show="frequency === 'weekly'" class="space-y-2">
                                            <flux:label>Repeat on Days</flux:label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach (['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $value => $label)
                                                    <label class="cursor-pointer">
                                                        <input type="checkbox" wire:model="repeatDays" value="{{ $value }}" class="sr-only peer" />
                                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border-2 border-zinc-200 dark:border-zinc-700 peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white text-sm font-medium text-zinc-700 dark:text-zinc-300 transition-colors">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <flux:error name="repeatDays" />
                                        </div>

                                        {{-- Ends --}}
                                        <div class="space-y-3">
                                            <flux:label>Ends</flux:label>
                                            <div class="space-y-2">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" wire:model.live="repeatEndsOption" value="never" x-model="endsOption" class="text-blue-500" />
                                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">Never</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" wire:model.live="repeatEndsOption" value="on" x-model="endsOption" class="text-blue-500" />
                                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">On date</span>
                                                    <div x-show="endsOption === 'on'" class="flex-1">
                                                        <flux:input wire:model="repeatEndsAt" type="date" />
                                                        <flux:error name="repeatEndsAt" />
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Call to Action Buttons --}}
                    <div
                        x-data="{ open: false }"
                        class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between p-4 text-left"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Call to Action Buttons</span>
                                <flux:badge size="sm" variant="outline">Optional</flux:badge>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="px-4 pb-4">
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <flux:description>Displayed below the event content.</flux:description>
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
                                                    <flux:input wire:model="ctaButtons.{{ $index }}.text" type="text" placeholder="Register Now" />
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
                    </div>

                    {{-- Photo Gallery --}}
                    <div
                        x-data="{ open: false }"
                        class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between p-4 text-left"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Photo Gallery</span>
                                <flux:badge size="sm" variant="outline">Optional</flux:badge>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="px-4 pb-4">
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                                <flux:description>Displayed below the event content and buttons.</flux:description>

                                @if (count($galleryImages) > 0)
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($galleryImages as $index => $item)
                                            <div wire:key="gallery-{{ $index }}" class="space-y-1">
                                                <div class="relative group aspect-square">
                                                    <img
                                                        src="{{ Storage::disk('public')->url($item['path']) }}"
                                                        alt="{{ $item['alt'] ?: 'Gallery image ' . ($index + 1) }}"
                                                        class="w-full h-full object-cover rounded-md"
                                                    />
                                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30 rounded-md">
                                                        <button
                                                            type="button"
                                                            wire:click="removeGalleryImage({{ $index }})"
                                                            wire:confirm="Remove this image from the gallery?"
                                                            class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 transition-colors"
                                                        >Remove</button>
                                                    </div>
                                                </div>
                                                <flux:input wire:model="galleryImages.{{ $index }}.alt" type="text" placeholder="Alt text…" />
                                                <flux:error name="galleryImages.{{ $index }}.alt" />
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-center text-zinc-400 dark:text-zinc-500 py-1">No photos added yet.</p>
                                @endif

                                <div
                                    x-data="{
                                        uploading: false,
                                        handleFile(event) {
                                            const file = event.target.files[0];
                                            if (!file) return;

                                            const maxWidth = 1920;
                                            const reader = new FileReader();

                                            reader.onload = (e) => {
                                                const img = new Image();
                                                img.onload = () => {
                                                    if (img.width <= maxWidth) {
                                                        this.uploading = true;
                                                        $wire.upload('newGalleryImage', file, () => {
                                                            $wire.call('addGalleryImage').then(() => {
                                                                this.uploading = false;
                                                                this.$refs.galleryFileInput.value = '';
                                                            });
                                                        });
                                                        return;
                                                    }

                                                    const scale = maxWidth / img.width;
                                                    const canvas = document.createElement('canvas');
                                                    canvas.width = maxWidth;
                                                    canvas.height = Math.round(img.height * scale);
                                                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);

                                                    this.uploading = true;

                                                    canvas.toBlob((blob) => {
                                                        $wire.upload(
                                                            'newGalleryImage',
                                                            new File([blob], file.name, { type: blob.type }),
                                                            () => {
                                                                $wire.call('addGalleryImage').then(() => {
                                                                    this.uploading = false;
                                                                    this.$refs.galleryFileInput.value = '';
                                                                });
                                                            }
                                                        );
                                                    }, file.type, 0.90);
                                                };
                                                img.src = e.target.result;
                                            };

                                            reader.readAsDataURL(file);
                                        }
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="file"
                                            x-ref="galleryFileInput"
                                            @change="handleFile($event)"
                                            accept="image/*"
                                            :disabled="uploading"
                                            class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-zinc-300 dark:file:border-zinc-600 file:text-sm file:font-medium file:bg-zinc-50 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-100 dark:hover:file:bg-zinc-700 transition-colors disabled:opacity-50"
                                        />
                                        <span x-show="uploading" class="text-xs text-zinc-500 dark:text-zinc-400 shrink-0">Uploading…</span>
                                    </div>
                                    <flux:error name="newGalleryImage" />
                                </div>

                                <flux:field>
                                    <flux:label>Photos per row</flux:label>
                                    <flux:select wire:model="galleryColumns">
                                        <flux:select.option value="2">2 per row</flux:select.option>
                                        <flux:select.option value="3">3 per row</flux:select.option>
                                        <flux:select.option value="4">4 per row (default)</flux:select.option>
                                        <flux:select.option value="5">5 per row</flux:select.option>
                                    </flux:select>
                                    <flux:error name="galleryColumns" />
                                </flux:field>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Settings --}}
                    <div
                        x-data="{ open: false }"
                        class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between p-4 text-left"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">SEO Settings</span>
                                <flux:badge size="sm" variant="outline">Optional</flux:badge>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="px-4 pb-4 space-y-4">
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">

                                <div x-data="{ length: $wire.metaTitle.length }">
                                    <flux:field>
                                        <div class="flex items-center justify-between mb-1">
                                            <flux:label class="mb-0">Meta Title</flux:label>
                                            <span class="text-xs tabular-nums" :class="length > 60 ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400'" x-text="length + ' / 60'"></span>
                                        </div>
                                        <flux:input
                                            wire:model="metaTitle"
                                            x-on:input="length = $event.target.value.length"
                                            type="text"
                                            placeholder="Defaults to event title…"
                                        />
                                        <flux:description>50–60 characters recommended. Shown in browser tabs and search results.</flux:description>
                                        <flux:error name="metaTitle" />
                                    </flux:field>
                                </div>

                                <div x-data="{ length: $wire.metaDescription.length }">
                                    <flux:field>
                                        <div class="flex items-center justify-between mb-1">
                                            <flux:label class="mb-0">Meta Description</flux:label>
                                            <span class="text-xs tabular-nums" :class="length > 160 ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400'" x-text="length + ' / 160'"></span>
                                        </div>
                                        <flux:textarea
                                            wire:model="metaDescription"
                                            x-on:input="length = $event.target.value.length"
                                            rows="3"
                                            placeholder="Defaults to event excerpt…"
                                        />
                                        <flux:description>150–160 characters recommended. Shown in search result snippets.</flux:description>
                                        <flux:error name="metaDescription" />
                                    </flux:field>
                                </div>

                                <flux:switch wire:model="isNoindex" label="Hide from search engines (noindex)" description="Prevents this event from appearing in search results." />

                                <div x-data="{ ogOpen: false }" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                    <button
                                        type="button"
                                        @click="ogOpen = !ogOpen"
                                        class="w-full flex items-center justify-between text-left"
                                    >
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Open Graph</p>
                                            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Customize how this event appears when shared on social media.</p>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-200 shrink-0 ml-3" :class="ogOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="ogOpen" x-transition class="mt-4">
                                        <flux:field>
                                            <flux:label>OG Image URL</flux:label>
                                            <flux:input wire:model="ogImage" type="url" placeholder="Defaults to featured image…" />
                                            <flux:description>Paste a full URL to a 1200×630px image for social sharing previews.</flux:description>
                                            <flux:error name="ogImage" />
                                        </flux:field>
                                    </div>
                                </div>

                            </div>
                        </div>
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
                            Update Event
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
                                <flux:menu.item :href="route('dashboard.events.index')" wire:navigate icon="x-mark">Cancel</flux:menu.item>
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

                        @if ($event->featured_image)
                            <div x-show="!preview" class="relative group">
                                <img src="{{ $event->featuredImageUrl() }}" alt="Current featured image" class="h-36 w-full object-cover rounded-md" />
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

                        <flux:field>
                            <flux:label>Alt Text</flux:label>
                            <flux:input wire:model="featuredImageAlt" type="text" :placeholder="$title ?: 'Defaults to event title'" />
                            <flux:error name="featuredImageAlt" />
                        </flux:field>
                    </div>

                    {{-- Layout --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4 space-y-3">
                        <flux:label>Layout</flux:label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="layout" value="image-top" class="sr-only peer" />
                                <div class="rounded-md border-2 border-zinc-200 dark:border-zinc-700 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 p-2 transition-colors">
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

                    {{-- Status --}}
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
                    </div>

                </div>
            </div>
        </form>
    </flux:main>
</div>

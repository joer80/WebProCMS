<?php

use App\Models\ContentItem;
use App\Support\ShortcodeProcessor;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    // ROW:php:start:content-show:z6QR7I
    public ContentItem $item;

    public function mount(int $id): void
    {
        $this->item = ContentItem::query()
            ->where('type_slug', 'meeting-notes')
            ->where('status', 'published')
            ->findOrFail($id);

        ShortcodeProcessor::setItemContext($this->item->data ?? []);
    }

    public function title(): string
    {
        return $this->item->displayTitle().' — '.config('app.name');
    }
    // ROW:php:end:content-show:z6QR7I
}; ?>
<div>
{{-- ROW:start:content-show:z6QR7I --}}
<x-dl.section slug="content-show:z6QR7I"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">

    <x-dl.wrapper slug="content-show:z6QR7I" prefix="breadcrumb" tag="nav"
        default-classes="mb-6 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('meeting-notes.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">Meeting Notes</a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-zinc-100 truncate">{{ $item->displayTitle() }}</span>
    </x-dl.wrapper>

    <x-dl.wrapper slug="content-show:z6QR7I" prefix="item_title" tag="h1"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-2">
        {{ $item->displayTitle() }}
    </x-dl.wrapper>

    @if ($item->published_at)
        <x-dl.wrapper slug="content-show:z6QR7I" prefix="item_date" tag="p"
            default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            {{ $item->published_at->format('F j, Y') }}
        </x-dl.wrapper>
    @endif

    <x-dl.wrapper slug="content-show:z6QR7I" prefix="back_link" tag="div"
        default-classes="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-700">
        <a href="{{ route('meeting-notes.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
            Back to Meeting Notes
        </a>
    </x-dl.wrapper>

</x-dl.section>
{{-- ROW:end:content-show:z6QR7I --}}
</div>

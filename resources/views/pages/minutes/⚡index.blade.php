<?php

use App\Models\ContentItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.public')] #[Title('Minutes')] class extends Component {
    use WithPagination;

    // ROW:php:start:content-index:l7IJdj
    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<ContentItem> */
    public function getItemsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ContentItem::query()
            ->where('type_slug', 'minutes')
            ->published()
            ->latest('published_at')
            ->paginate(20);
    }
    // ROW:php:end:content-index:l7IJdj
}; ?>
<div>
{{-- ROW:start:content-index:l7IJdj --}}
<x-dl.section slug="content-index:l7IJdj"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="content-index:l7IJdj" prefix="heading" default="Minutes"
        default-tag="h1"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.wrapper slug="content-index:l7IJdj" prefix="items_list"
        default-classes="divide-y divide-zinc-200 dark:divide-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @forelse ($this->items as $item)
            <x-dl.card slug="content-index:l7IJdj" prefix="item_card"
                default-classes="px-5 py-4 bg-white dark:bg-zinc-900 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <x-dl.wrapper slug="content-index:l7IJdj" prefix="item_title" tag="a"
                    href="{{ route('minutes.show', $item->id) }}"
                    default-classes="font-medium text-zinc-900 dark:text-zinc-100 hover:text-primary transition-colors">
                    {{ $item->displayTitle() }}
                </x-dl.wrapper>
                @if ($item->published_at)
                    <x-dl.wrapper slug="content-index:l7IJdj" prefix="item_date" tag="span"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 shrink-0">
                        {{ $item->published_at->format('M j, Y') }}
                    </x-dl.wrapper>
                @endif
            </x-dl.card>
        @empty
            <x-dl.wrapper slug="content-index:l7IJdj" prefix="empty_state" tag="p"
                default-classes="px-5 py-10 text-center text-zinc-500 dark:text-zinc-400">
                No Minutes found.
            </x-dl.wrapper>
        @endforelse
    </x-dl.wrapper>
    <div class="mt-6">
        {{ $this->items->links() }}
    </div>
</x-dl.section>
{{-- ROW:end:content-index:l7IJdj --}}
</div>

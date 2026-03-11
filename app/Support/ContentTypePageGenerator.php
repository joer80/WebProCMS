<?php

namespace App\Support;

use App\Models\ContentTypeDefinition;
use Illuminate\Support\Str;

class ContentTypePageGenerator
{
    public function generate(ContentTypeDefinition $typeDef): void
    {
        $this->createDirectory($typeDef->slug);
        $this->writeIndexPage($typeDef);
        $this->writeShowPage($typeDef);
        $this->injectRoutes($typeDef->slug);
    }

    public function hasPages(string $slug): bool
    {
        return file_exists(resource_path("views/pages/{$slug}/⚡index.blade.php"));
    }

    private function createDirectory(string $slug): void
    {
        $dir = resource_path("views/pages/{$slug}");

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function writeIndexPage(ContentTypeDefinition $typeDef): void
    {
        $slug = $typeDef->slug;
        $name = $typeDef->name;
        $rowSlug = 'content-index:'.Str::random(6);

        $blade = $this->buildIndexBlade($typeDef, $rowSlug);
        $php = $this->buildIndexPhp($slug, $name, $rowSlug);

        file_put_contents(
            resource_path("views/pages/{$slug}/⚡index.blade.php"),
            $php."\n<div>".$blade."\n</div>\n"
        );
    }

    private function writeShowPage(ContentTypeDefinition $typeDef): void
    {
        $slug = $typeDef->slug;
        $rowSlug = 'content-show:'.Str::random(6);

        $blade = $this->buildShowBlade($typeDef, $rowSlug);
        $php = $this->buildShowPhp($slug, $typeDef->name, $rowSlug);

        file_put_contents(
            resource_path("views/pages/{$slug}/⚡show.blade.php"),
            $php."\n<div>".$blade."\n</div>\n"
        );
    }

    private function buildIndexPhp(string $slug, string $name, string $rowSlug): string
    {
        return <<<PHP
<?php

use App\Models\ContentItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.public')] #[Title('{$name}')] class extends Component {
    use WithPagination;

    // ROW:php:start:{$rowSlug}
    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<ContentItem> */
    public function getItemsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ContentItem::query()
            ->where('type_slug', '{$slug}')
            ->published()
            ->latest('published_at')
            ->paginate(20);
    }
    // ROW:php:end:{$rowSlug}
}; ?>
PHP;
    }

    private function buildShowPhp(string $slug, string $name, string $rowSlug): string
    {
        return <<<PHP
<?php

use App\Models\ContentItem;
use App\Support\ShortcodeProcessor;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    // ROW:php:start:{$rowSlug}
    public ContentItem \$item;

    public function mount(int \$id): void
    {
        \$this->item = ContentItem::query()
            ->where('type_slug', '{$slug}')
            ->where('status', 'published')
            ->findOrFail(\$id);

        ShortcodeProcessor::setItemContext(\$this->item->data ?? []);
    }

    public function title(): string
    {
        return \$this->item->displayTitle().' — '.config('app.name');
    }
    // ROW:php:end:{$rowSlug}
}; ?>
PHP;
    }

    private function buildIndexBlade(ContentTypeDefinition $typeDef, string $rowSlug): string
    {
        $slug = $typeDef->slug;
        $name = $typeDef->name;

        return <<<BLADE

{{-- ROW:start:{$rowSlug} --}}
<x-dl.section slug="{$rowSlug}"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="{$rowSlug}" prefix="heading" default="{$name}"
        default-tag="h1"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.wrapper slug="{$rowSlug}" prefix="items_list"
        default-classes="divide-y divide-zinc-200 dark:divide-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @forelse (\$this->items as \$item)
            <x-dl.card slug="{$rowSlug}" prefix="item_card"
                default-classes="px-5 py-4 bg-white dark:bg-zinc-900 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <x-dl.wrapper slug="{$rowSlug}" prefix="item_title" tag="a"
                    href="{{ route('{$slug}.show', \$item->id) }}"
                    default-classes="font-medium text-zinc-900 dark:text-zinc-100 hover:text-primary transition-colors">
                    {{ \$item->displayTitle() }}
                </x-dl.wrapper>
                @if (\$item->published_at)
                    <x-dl.wrapper slug="{$rowSlug}" prefix="item_date" tag="span"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 shrink-0">
                        {{ \$item->published_at->format('M j, Y') }}
                    </x-dl.wrapper>
                @endif
            </x-dl.card>
        @empty
            <x-dl.wrapper slug="{$rowSlug}" prefix="empty_state" tag="p"
                default-classes="px-5 py-10 text-center text-zinc-500 dark:text-zinc-400">
                No {$name} found.
            </x-dl.wrapper>
        @endforelse
    </x-dl.wrapper>
    <div class="mt-6">
        {{ \$this->items->links() }}
    </div>
</x-dl.section>
{{-- ROW:end:{$rowSlug} --}}
BLADE;
    }

    private function buildShowBlade(ContentTypeDefinition $typeDef, string $rowSlug): string
    {
        $slug = $typeDef->slug;
        $name = $typeDef->name;
        $fieldBlocks = $this->buildFieldBlocks($typeDef->fields, $rowSlug);

        return <<<BLADE

{{-- ROW:start:{$rowSlug} --}}
<x-dl.section slug="{$rowSlug}"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">

    <x-dl.wrapper slug="{$rowSlug}" prefix="breadcrumb" tag="nav"
        default-classes="mb-6 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('{$slug}.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">{$name}</a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-zinc-100 truncate">{{ \$item->displayTitle() }}</span>
    </x-dl.wrapper>

    <x-dl.wrapper slug="{$rowSlug}" prefix="item_title" tag="h1"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-2">
        {{ \$item->displayTitle() }}
    </x-dl.wrapper>

    @if (\$item->published_at)
        <x-dl.wrapper slug="{$rowSlug}" prefix="item_date" tag="p"
            default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            {{ \$item->published_at->format('F j, Y') }}
        </x-dl.wrapper>
    @endif
{$fieldBlocks}
    <x-dl.wrapper slug="{$rowSlug}" prefix="back_link" tag="div"
        default-classes="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-700">
        <a href="{{ route('{$slug}.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
            Back to {$name}
        </a>
    </x-dl.wrapper>

</x-dl.section>
{{-- ROW:end:{$rowSlug} --}}
BLADE;
    }

    /**
     * @param  array<int, array{label: string, name: string, type: string, options: string, required: bool}>  $fields
     */
    private function buildFieldBlocks(array $fields, string $rowSlug): string
    {
        $blocks = '';
        $isFirst = true;

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldLabel = $field['label'] ?? '';
            $fieldType = $field['type'] ?? 'text';
            $prefix = Str::snake($fieldName);

            // Skip the first field — it's already used as the page title
            if ($isFirst) {
                $isFirst = false;

                continue;
            }

            $blocks .= match ($fieldType) {
                'richtext', 'richtext_tiptap' => $this->richtextBlock($rowSlug, $prefix, $fieldName, $fieldLabel, $fieldType),
                'date' => $this->dateBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                'image' => $this->imageBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                'gallery' => $this->galleryBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                'file' => $this->fileBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                'files' => $this->filesBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                'oembed' => $this->oembedBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
                default => $this->textBlock($rowSlug, $prefix, $fieldName, $fieldLabel),
            };
        }

        return $blocks;
    }

    private function richtextBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel, string $type): string
    {
        $method = $type === 'richtext_tiptap' ? 'processRaw' : 'process';

        return <<<BLADE

    <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_label" tag="h2"
        default-classes="font-heading text-xl font-semibold text-zinc-900 dark:text-white mt-10 mb-3">
        {$fieldLabel}
    </x-dl.wrapper>
    <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_content"
        default-classes="prose dark:prose-invert max-w-none">
        {!! isset(\$item->data['{$fieldName}']) ? \\App\\Support\\ShortcodeProcessor::{$method}((string) \$item->data['{$fieldName}']) : '' !!}
    </x-dl.wrapper>
BLADE;
    }

    private function dateBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_block" tag="div"
        default-classes="mt-6 flex items-center gap-2 text-sm">
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_label" tag="span"
            default-classes="font-medium text-zinc-500 dark:text-zinc-400">
            {$fieldLabel}:
        </x-dl.wrapper>
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_value" tag="span"
            default-classes="text-zinc-900 dark:text-zinc-100">
            {{ !empty(\$item->data['{$fieldName}']) ? \\Carbon\\Carbon::parse(\$item->data['{$fieldName}'])->format('F j, Y') : '—' }}
        </x-dl.wrapper>
    </x-dl.wrapper>
BLADE;
    }

    private function imageBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    @if (!empty(\$item->data['{$fieldName}']))
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_image_wrapper"
            default-classes="mt-8 rounded-card overflow-hidden">
            <img src="{{ \\Illuminate\\Support\\Facades\\Storage::url(\$item->data['{$fieldName}']) }}"
                 alt="{$fieldLabel}"
                 class="w-full h-auto object-cover" />
        </x-dl.wrapper>
    @endif
BLADE;
    }

    private function galleryBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    @if (!empty(\$item->data['{$fieldName}']))
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_label" tag="h2"
            default-classes="font-heading text-xl font-semibold text-zinc-900 dark:text-white mt-10 mb-4">
            {$fieldLabel}
        </x-dl.wrapper>
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_grid"
            default-classes="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ((array) \$item->data['{$fieldName}'] as \$gImg)
                <div class="aspect-square overflow-hidden rounded-lg">
                    <img src="{{ \\Illuminate\\Support\\Facades\\Storage::url(\$gImg['path']) }}"
                         alt="{{ \$gImg['alt'] ?? '' }}"
                         class="w-full h-full object-cover" />
                </div>
            @endforeach
        </x-dl.wrapper>
    @endif
BLADE;
    }

    private function fileBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    @if (!empty(\$item->data['{$fieldName}']) && is_array(\$item->data['{$fieldName}']))
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_block" tag="div"
            default-classes="mt-6">
            <a href="{{ \\Illuminate\\Support\\Facades\\Storage::disk('public')->url(\$item->data['{$fieldName}']['path']) }}"
               target="_blank"
               class="inline-flex items-center gap-2 text-primary hover:underline text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                {{ \$item->data['{$fieldName}']['name'] ?? '{$fieldLabel}' }}
            </a>
        </x-dl.wrapper>
    @endif
BLADE;
    }

    private function filesBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    @if (!empty(\$item->data['{$fieldName}']) && is_array(\$item->data['{$fieldName}']))
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_label" tag="h2"
            default-classes="font-heading text-xl font-semibold text-zinc-900 dark:text-white mt-10 mb-3">
            {$fieldLabel}
        </x-dl.wrapper>
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_list" tag="ul"
            default-classes="space-y-2">
            @foreach ((array) \$item->data['{$fieldName}'] as \$fItem)
                <li>
                    <a href="{{ \\Illuminate\\Support\\Facades\\Storage::disk('public')->url(\$fItem['path']) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 text-primary hover:underline text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                        {{ \$fItem['name'] }}
                    </a>
                </li>
            @endforeach
        </x-dl.wrapper>
    @endif
BLADE;
    }

    private function oembedBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    @if (!empty(\$item->data['{$fieldName}']))
        <x-dl.video slug="{$rowSlug}" prefix="{$prefix}"
            default-wrapper-classes="mt-8 rounded-card overflow-hidden aspect-video"
            default-video-classes="w-full h-full"
            default-video-url="{{ \$item->data['{$fieldName}'] ?? '' }}" />
    @endif
BLADE;
    }

    private function textBlock(string $rowSlug, string $prefix, string $fieldName, string $fieldLabel): string
    {
        return <<<BLADE

    <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_block" tag="div"
        default-classes="mt-4 flex items-center gap-2 text-sm">
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_label" tag="span"
            default-classes="font-medium text-zinc-500 dark:text-zinc-400">
            {$fieldLabel}:
        </x-dl.wrapper>
        <x-dl.wrapper slug="{$rowSlug}" prefix="{$prefix}_value" tag="span"
            default-classes="text-zinc-900 dark:text-zinc-100">
            {{ \$item->data['{$fieldName}'] ?? '—' }}
        </x-dl.wrapper>
    </x-dl.wrapper>
BLADE;
    }

    private function injectRoutes(string $slug): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);

        // Skip if routes already exist
        if (str_contains($contents, "'{$slug}.index'")) {
            return;
        }

        $indexLine = "    Route::livewire('{$slug}', 'pages::{$slug}.index')->name('{$slug}.index');";
        $showLine = "    Route::livewire('{$slug}/{id}', 'pages::{$slug}.show')->name('{$slug}.show');";

        $contents = preg_replace(
            '/^(    \/\/ new cached pages are inserted here)$/m',
            "$1\n{$indexLine}\n{$showLine}",
            $contents,
            1
        );

        file_put_contents($routesPath, $contents);
    }
}

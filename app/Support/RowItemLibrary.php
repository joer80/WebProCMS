<?php

namespace App\Support;

class RowItemLibrary
{
    /**
     * Available items that can be added to a row via the content editor.
     *
     * Each item has a name, icon (heroicon), optional default prefix, and a blade
     * snippet using __SLUG__ and __PREFIX__ placeholders.
     *
     * @return array<string, array{name: string, icon: string, prefix?: string, blade: string}>
     */
    public static function items(): array
    {
        return [
            'heading' => [
                'name' => 'Heading',
                'icon' => 'document-text',
                'prefix' => 'headline',
                'blade' => <<<'BLADE'
    <x-dl.heading slug="__SLUG__" prefix="__PREFIX__" default="Your Heading"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
BLADE,
            ],
            'subheadline' => [
                'name' => 'Subheadline',
                'icon' => 'bars-3-bottom-left',
                'prefix' => 'subheadline',
                'blade' => <<<'BLADE'
    <x-dl.subheadline slug="__SLUG__" prefix="__PREFIX__" default="Supporting text goes here."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
BLADE,
            ],
            'buttons' => [
                'name' => 'Buttons',
                'icon' => 'cursor-arrow-rays',
                'blade' => <<<'BLADE'
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center gap-4"
        default-primary-label="Get Started"
        default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Learn More"
        default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors" />
BLADE,
            ],
            'image' => [
                'name' => 'Image',
                'icon' => 'photo',
                'prefix' => 'section_image',
                'blade' => <<<'BLADE'
    <x-dl.image slug="__SLUG__" prefix="__PREFIX__"
        default-wrapper-classes="rounded-card overflow-hidden"
        default-image-classes="w-full h-auto object-cover" />
BLADE,
            ],
            'video' => [
                'name' => 'Video',
                'icon' => 'film',
                'prefix' => 'section_video',
                'blade' => <<<'BLADE'
    <x-dl.video slug="__SLUG__" prefix="__PREFIX__"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video"
        default-video-classes="w-full h-full"
        default-video-url="" />
BLADE,
            ],
            'link' => [
                'name' => 'Link',
                'icon' => 'link',
                'prefix' => 'section_link',
                'blade' => <<<'BLADE'
    <x-dl.link slug="__SLUG__" prefix="__PREFIX__"
        default-label="Learn More →"
        default-url="/"
        default-classes="text-primary font-semibold hover:text-primary/80 transition-colors" />
BLADE,
            ],
            'accordion' => [
                'name' => 'Accordion',
                'icon' => 'chevron-up-down',
                'prefix' => 'faqs',
                'blade' => <<<'BLADE'
    <x-dl.accordion slug="__SLUG__" prefix="__PREFIX__"
        default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"question":"Your question?","answer":"Your answer here."}]'>
        @dlItems('__SLUG__', '__PREFIX__', $__PREFIX__, '[{"question":"Your question?","answer":"Your answer here."}]')
        @foreach ($__PREFIX__ as $i => $item)
            <x-dl.accordion-item slug="__SLUG__" prefix="__PREFIX___item" :index="$i"
                question="{{ $item['question'] }}"
                default-classes="py-5"
                default-button-classes="w-full flex items-center justify-between text-left"
                default-question-classes="text-base font-semibold text-zinc-900 dark:text-white"
                default-chevron-classes="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
                default-answer-classes="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                {{ $item['answer'] }}
            </x-dl.accordion-item>
        @endforeach
    </x-dl.accordion>
BLADE,
            ],
        ];
    }
}

<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class AccordionItem extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'faq_item',
        public int $index = 0,
        public string $question = '',
        public string $defaultClasses = 'py-5',
        public string $defaultButtonClasses = 'w-full flex items-center justify-between text-left',
        public string $defaultQuestionClasses = 'text-base font-semibold text-zinc-900 dark:text-white',
        public string $defaultChevronClasses = 'size-5 text-zinc-400 shrink-0 transition-transform duration-200',
        public string $defaultAnswerClasses = 'mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'faq_item';

        return [
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'py-5'],
            ['key' => "{$prefix}_button_classes", 'default' => $attrs['default-button-classes'] ?? 'w-full flex items-center justify-between text-left'],
            ['key' => "{$prefix}_question_classes", 'default' => $attrs['default-question-classes'] ?? 'text-base font-semibold text-zinc-900 dark:text-white'],
            ['key' => "{$prefix}_chevron_classes", 'default' => $attrs['default-chevron-classes'] ?? 'size-5 text-zinc-400 shrink-0 transition-transform duration-200'],
            ['key' => "{$prefix}_answer_classes", 'default' => $attrs['default-answer-classes'] ?? 'mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.accordion-item');
    }
}

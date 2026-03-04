@blaze
@props([
    'slug',
    'prefix' => 'faq_item',
    'index' => 0,
    'question' => '',
    'defaultClasses' => 'py-5',
    'defaultButtonClasses' => 'w-full flex items-center justify-between text-left',
    'defaultQuestionClasses' => 'text-base font-semibold text-zinc-900 dark:text-white',
    'defaultChevronClasses' => 'size-5 text-zinc-400 shrink-0 transition-transform duration-200',
    'defaultAnswerClasses' => 'mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed',
])
@php
$itemCls = content($slug, "{$prefix}_classes", $defaultClasses);
$buttonCls = content($slug, "{$prefix}_button_classes", $defaultButtonClasses);
$questionCls = content($slug, "{$prefix}_question_classes", $defaultQuestionClasses);
$chevronCls = content($slug, "{$prefix}_chevron_classes", $defaultChevronClasses);
$answerCls = content($slug, "{$prefix}_answer_classes", $defaultAnswerClasses);
$itemId = content($slug, "{$prefix}_id", '');
$itemAttrsRaw = json_decode(content($slug, "{$prefix}_attrs", '[]'), true) ?: [];
$extraAttrs = $itemId ? ['id' => $itemId] : [];
foreach ($itemAttrsRaw as $attr) {
    if (!empty($attr['name'])) {
        $extraAttrs[$attr['name']] = $attr['value'] ?? '';
    }
}
@endphp
{!! '<div ' . $attributes->merge(array_merge(['class' => $itemCls], $extraAttrs))->toHtml() . '>' !!}
    <button class="{{ $buttonCls }}" x-on:click="open === {{ $index }} ? open = null : open = {{ $index }}">
        <span class="{{ $questionCls }}">{{ $question }}</span>
        <svg class="{{ $chevronCls }}" x-bind:class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div class="{{ $answerCls }}" x-show="open === {{ $index }}" x-collapse>
        {{ $slot }}
    </div>
</div>

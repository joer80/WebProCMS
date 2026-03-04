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

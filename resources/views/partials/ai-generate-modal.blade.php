<div
    x-data="{
        open: false,
        fieldKey: null,
        fieldType: null,
        fieldLabel: '',
        currentClasses: '',
        prompt: '',
        generating: false,
        error: '',
        async generate() {
            if (!this.prompt.trim()) return;
            this.generating = true;
            this.error = '';
            $wire.generateAiContent(this.fieldKey, this.prompt, this.fieldType, this.currentClasses);
        }
    }"
    @open-ai-generate.window="open = true; fieldKey = $event.detail.fieldKey; fieldType = $event.detail.fieldType; fieldLabel = $event.detail.fieldLabel || ''; currentClasses = $event.detail.currentClasses || ''; prompt = ''; error = '';"
    @ai-content-generated.window="if ($event.detail.fieldKey === fieldKey) { open = false; generating = false; prompt = ''; }"
    @ai-generate-error.window="if ($event.detail.fieldKey === fieldKey) { error = $event.detail.message; generating = false; }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    @keydown.escape.window="if (!generating) open = false"
>
    <div class="absolute inset-0 bg-black/50" @click="if (!generating) open = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Generate with AI</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5" x-text="fieldLabel ? 'Generating for: ' + fieldLabel : 'Enter a prompt to generate content.'"></p>
            </div>
            <button type="button" @click="if (!generating) open = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                <flux:icon name="x-mark" class="size-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5 block">Prompt</label>
                <textarea
                    x-model="prompt"
                    x-ref="promptInput"
                    rows="4"
                    :placeholder="fieldType === 'classes' ? 'Describe what to change… e.g. make the text smaller, add more padding' : 'Describe what content you want to generate…'"
                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); generate(); }"
                    :disabled="generating"
                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none disabled:opacity-60"
                ></textarea>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Tip: Press Enter to generate, Shift+Enter for a new line.</p>
            </div>
            <div x-show="error" x-transition class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-3 py-2 text-sm text-red-700 dark:text-red-400" x-text="error"></div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-zinc-200 dark:border-zinc-700">
            <button
                type="button"
                @click="if (!generating) open = false"
                :disabled="generating"
                class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors disabled:opacity-50"
            >Cancel</button>
            <button
                type="button"
                @click="generate()"
                :disabled="generating || !prompt.trim()"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <span x-show="generating">
                    <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span x-show="!generating"><flux:icon name="sparkles" class="size-4" /></span>
                <span x-text="generating ? 'Generating…' : 'Generate'"></span>
            </button>
        </div>
    </div>
</div>

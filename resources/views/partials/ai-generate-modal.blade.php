<div
    x-data="{
        open: false,
        mode: 'generate',
        fieldKey: null,
        fieldType: null,
        fieldLabel: '',
        currentClasses: '',
        prompt: '',
        tone: '',
        toneLabel: '',
        generating: false,
        error: '',
        useHtml: true,
        previewPath: null,
        previewUrl: null,
        lastPrompt: '',
        rowSlug: null,
        includeContext: true,
        gridItemIdx: null,
        gridItemKey: null,
        sentPrompt: '',
        showSentPrompt: false,
        contextSnippets: [],
        loadingContext: false,
        showContextPreview: false,
        closeModal() {
            if (this.previewPath) {
                $wire.discardAiImagePreview(this.previewPath);
                this.previewPath = null;
                this.previewUrl = null;
            }
            this.open = false;
        },
        async generate() {
            if (!this.prompt.trim() && !(this.fieldType === 'image' && this.includeContext)) return;
            this.generating = true;
            this.error = '';
            if (this.fieldType === 'image') {
                if (this.previewPath) {
                    $wire.discardAiImagePreview(this.previewPath);
                    this.previewPath = null;
                    this.previewUrl = null;
                }
                $wire.generateAiImage(this.fieldKey, this.prompt, this.rowSlug, this.includeContext, this.gridItemIdx ?? -1);
            } else {
                $wire.generateAiContent(this.fieldKey, this.prompt, this.fieldType, this.currentClasses, this.useHtml);
            }
        }
    }"
    @open-ai-generate.window="open = true; mode = 'generate'; fieldKey = $event.detail.fieldKey; fieldType = $event.detail.fieldType; fieldLabel = $event.detail.fieldLabel || ''; currentClasses = $event.detail.currentClasses || ''; rowSlug = $event.detail.rowSlug || null; gridItemIdx = $event.detail.gridItemIdx ?? null; gridItemKey = $event.detail.gridItemKey || null; prompt = ''; tone = ''; toneLabel = ''; error = ''; useHtml = true; previewPath = null; previewUrl = null; lastPrompt = ''; sentPrompt = ''; showSentPrompt = false; contextSnippets = []; loadingContext = false; showContextPreview = false; if ($event.detail.fieldType === 'alt') { generating = true; $wire.generateAiAltText($event.detail.fieldKey, $event.detail.imageFieldKey); }"
    @ai-image-context.window="if ($event.detail.fieldKey === fieldKey) { contextSnippets = $event.detail.snippets; loadingContext = false; showContextPreview = true; }"
    @open-ai-rewrite.window="open = true; mode = 'rewrite'; fieldKey = $event.detail.fieldKey; fieldType = $event.detail.fieldType; fieldLabel = $event.detail.fieldLabel || ''; tone = $event.detail.tone; toneLabel = $event.detail.toneLabel; prompt = ''; currentClasses = ''; error = ''; generating = true; $wire.rewriteAiContent($event.detail.fieldKey, $event.detail.fieldType, $event.detail.tone);"
    @ai-image-preview.window="if ($event.detail.fieldKey === fieldKey) { previewPath = $event.detail.tempPath; previewUrl = $event.detail.url; lastPrompt = prompt; sentPrompt = $event.detail.fullPrompt || ''; showSentPrompt = false; generating = false; }"
    @ai-content-generated.window="if ($event.detail.fieldKey === fieldKey) { open = false; generating = false; prompt = ''; }"
    @ai-image-generated.window="if ($event.detail.fieldKey === fieldKey) { open = false; generating = false; prompt = ''; previewPath = null; previewUrl = null; lastPrompt = ''; }"
    @ai-generate-error.window="if ($event.detail.fieldKey === fieldKey) { error = $event.detail.message; generating = false; }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    @keydown.escape.window="if (!generating) closeModal()"
>
    <div class="absolute inset-0 bg-black/50" @click="if (!generating) closeModal()"></div>

    <div class="relative w-full bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700"
        :class="previewUrl ? 'max-w-2xl' : 'max-w-lg'">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100" x-text="mode === 'rewrite' ? 'Rewrite with AI' : 'Generate with AI'"></h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5" x-text="mode === 'rewrite' ? (fieldLabel ? 'Rewriting: ' + fieldLabel : 'Rewriting content…') : (fieldLabel ? 'Generating for: ' + fieldLabel : 'Enter a prompt to generate content.')"></p>
            </div>
            <button type="button" @click="if (!generating) closeModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                <flux:icon name="x-mark" class="size-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-4">
            <div x-show="mode === 'rewrite'" class="py-2 text-center">
                <div class="flex items-center justify-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <svg x-show="generating" class="size-4 animate-spin shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="generating" x-text="'Rewriting as ' + toneLabel + '…'"></span>
                </div>
            </div>
            <div x-show="fieldType === 'alt' && mode === 'generate'" class="py-2 text-center">
                <div class="flex items-center justify-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <svg class="size-4 animate-spin shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Analyzing image for alt text…</span>
                </div>
            </div>
            <div x-show="mode === 'generate' && fieldType !== 'alt'">

                {{-- Image preview --}}
                <div x-show="fieldType === 'image' && previewUrl" class="mb-4">
                    <div class="rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                        <img :src="previewUrl" alt="Generated image preview" class="w-full">
                    </div>
                    <div x-show="sentPrompt" class="mt-1.5">
                        <button type="button" @click="showSentPrompt = !showSentPrompt"
                            class="flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                            <flux:icon name="eye" class="size-3" />
                            <span x-text="showSentPrompt ? 'Hide prompt sent to AI' : 'View prompt sent to AI'"></span>
                        </button>
                        <div x-show="showSentPrompt" x-transition class="mt-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-xs text-zinc-600 dark:text-zinc-300 whitespace-pre-wrap font-mono" x-text="sentPrompt"></div>
                    </div>
                </div>

                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5 block">Prompt</label>
                <textarea
                    x-model="prompt"
                    x-ref="promptInput"
                    rows="4"
                    :placeholder="fieldType === 'classes' ? 'Describe what to change… e.g. make the text smaller, add more padding' : fieldType === 'seo' ? 'Describe the page content, target audience, and main keyword… e.g. Plumber in Austin TX, targeting homeowners needing emergency repairs' : fieldType === 'image' ? 'Describe the image you want to generate… e.g. A modern office lobby with warm lighting and plants' : 'Describe what content you want to generate…'"
                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); generate(); }"
                    :disabled="generating"
                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none disabled:opacity-60"
                ></textarea>
                <div x-show="fieldType === 'richtext'" class="flex items-center justify-between mt-2">
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Tip: Press Enter to generate, Shift+Enter for a new line.</p>
                    <button
                        type="button"
                        @click="useHtml = !useHtml"
                        :disabled="generating"
                        class="flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full border transition-colors disabled:opacity-50"
                        :class="useHtml ? 'bg-primary/10 border-primary/30 text-primary dark:bg-primary/20' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 text-zinc-500 dark:text-zinc-400'"
                    >
                        <flux:icon name="code-bracket" class="size-3" />
                        <span x-text="useHtml ? 'HTML' : 'Plain text'"></span>
                    </button>
                </div>
                <p x-show="fieldType !== 'image' && fieldType !== 'richtext'" class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Tip: Press Enter to generate, Shift+Enter for a new line.</p>
                <div x-show="fieldType === 'image'" class="mt-2 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="ai-include-context" x-model="includeContext" :disabled="generating"
                            @change="contextSnippets = []; showContextPreview = false;"
                            class="rounded border-zinc-300 dark:border-zinc-600 text-primary focus:ring-primary focus:ring-offset-0 disabled:opacity-50 cursor-pointer">
                        <label for="ai-include-context" class="text-xs text-zinc-500 dark:text-zinc-400 cursor-pointer select-none flex-1">Provide AI with text used around this image for context</label>
                        <button x-show="includeContext && rowSlug" type="button"
                            @click="if (!loadingContext) { if (showContextPreview && contextSnippets.length) { showContextPreview = false; } else { loadingContext = true; $wire.loadAiImageContext(fieldKey, rowSlug, gridItemIdx ?? -1); } }"
                            :disabled="generating"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors disabled:opacity-50 shrink-0">
                            <template x-if="loadingContext">
                                <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            </template>
                            <template x-if="!loadingContext">
                                <flux:icon name="eye" class="size-3.5" />
                            </template>
                        </button>
                    </div>
                    <div x-show="showContextPreview" x-transition>
                        <template x-if="contextSnippets.length">
                            <ul class="rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-3 py-2 space-y-0.5">
                                <template x-for="snippet in contextSnippets" :key="snippet">
                                    <li class="text-xs text-zinc-600 dark:text-zinc-300 font-mono truncate" x-text="'• ' + snippet"></li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="!contextSnippets.length">
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">No saved text content found for this section yet.</p>
                        </template>
                    </div>
                </div>
                <p x-show="fieldType === 'image' && !previewUrl" class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Image generation may take 10–20 seconds.</p>
                <p x-show="fieldType === 'image' && previewUrl" class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Keep your original description and add style changes — don't replace it entirely.</p>
            </div>
            <div x-show="error" x-transition class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-3 py-2 text-sm text-red-700 dark:text-red-400" x-text="error"></div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-zinc-200 dark:border-zinc-700">
            <button
                x-show="!(fieldType === 'image' && previewUrl)"
                type="button"
                @click="if (!generating) closeModal()"
                :disabled="generating"
                class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors disabled:opacity-50"
            >Cancel</button>

            {{-- Discard (shown when preview is ready) --}}
            <button
                x-show="fieldType === 'image' && previewUrl"
                type="button"
                @click="$wire.discardAiImagePreview(previewPath); previewPath = null; previewUrl = null; lastPrompt = '';"
                :disabled="generating"
                class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors disabled:opacity-50"
            >Discard</button>

            {{-- Generate / Regenerate button --}}
            <button
                x-show="mode === 'generate' && fieldType !== 'alt'"
                type="button"
                @click="generate()"
                :disabled="generating || (!prompt.trim() && !(fieldType === 'image' && includeContext))"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :class="(fieldType === 'image' && previewUrl) ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-600' : 'bg-primary text-white hover:bg-primary/90'"
            >
                <span x-show="generating">
                    <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span x-show="!generating"><flux:icon name="sparkles" class="size-4" /></span>
                <span x-text="generating ? 'Generating…' : (fieldType === 'image' && previewUrl ? 'Regenerate' : 'Generate')"></span>
            </button>

            {{-- Save to Library (shown when preview is ready) --}}
            <button
                x-show="fieldType === 'image' && previewUrl"
                type="button"
                @click="generating = true; gridItemIdx !== null ? $wire.saveAiGridItemImagePreview(fieldKey, previewPath, prompt, gridItemIdx, gridItemKey) : $wire.saveAiImagePreview(fieldKey, previewPath, prompt)"
                :disabled="generating"
                class="flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white text-sm font-medium rounded-lg hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <flux:icon name="arrow-down-tray" class="size-4" />
                <span>Save to Library</span>
            </button>
        </div>
    </div>
</div>

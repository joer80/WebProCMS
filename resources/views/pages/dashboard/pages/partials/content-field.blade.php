@php
$effMode = "(groupMode !== null ? groupMode : (advancedMode ? 'advanced' : (designMode ? 'design' : 'content')))";
if (in_array($field['type'], ['id', 'attrs'])) {
    $fieldShow = "{$effMode} === 'advanced'";
} elseif (in_array($field['type'], ['classes', 'object_fit', 'border_radius', 'animation', 'animation_delay', 'bg_position', 'bg_size', 'bg_repeat'])) {
    $fieldShow = "{$effMode} === 'design'";
} else {
    $fieldShow = "{$effMode} === 'content'";
}
$isAltField = $field['type'] === 'text' && str_ends_with($field['key'], '_alt');
$showShortcodeBtn = ($field['type'] === 'text' && ! str_ends_with($field['key'], '_url') && ! str_ends_with($field['key'], '_htag') && ! $isAltField) || $field['key'] === 'subheadline';
$pageTypeSlug = preg_match('#^pages/([^/]+)/⚡show\.blade\.php$#u', $file ?? '', $ptm) ? $ptm[1] : '';
$aiEnabled = (bool) (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'));
// Language translation: detect if this is a non-English variant field (key ends with __xx).
$isLangVariant = (bool) preg_match('/__([a-z]{2,10})$/', $field['key'], $_langMatch);
$fieldLangCode = $isLangVariant ? $_langMatch[1] : null;
$showTranslateBtn = $isLangVariant && $aiEnabled && in_array($field['type'], ['text', 'richtext'], true);
$showAiBtn = $aiEnabled && ! $isAltField && ($showShortcodeBtn || $field['type'] === 'richtext' || $field['type'] === 'classes');
$showAiRewriteBtn = $aiEnabled && ! $isAltField && ($showShortcodeBtn || $field['type'] === 'richtext');
$showAiAltBtn = $aiEnabled && $isAltField;
$aiImageEnabled = (bool) (\App\Models\Setting::get('ai.openai_key') || \App\Models\Setting::get('ai.fal_key') || \App\Models\Setting::get('ai.stability_key'));
$showAiImageBtn = $aiImageEnabled && $field['type'] === 'image';
$showLoremBtn = $showShortcodeBtn || $field['type'] === 'richtext';
$loremDefaultLen = strlen(strip_tags($field['default'] ?? ''));
$loremDefaultSize = $loremDefaultLen <= 50 ? 'sentence' : ($loremDefaultLen <= 100 ? 'short' : ($loremDefaultLen <= 300 ? 'medium' : 'long'));
$themeColorNames = [];
if ($field['type'] === 'classes') {
    try {
        $pubCss = resource_path('css/public.css');
        if (file_exists($pubCss)) {
            preg_match('/@theme\s*\{([^}]+)\}/s', file_get_contents($pubCss), $themeBlock);
            if (! empty($themeBlock[1])) {
                preg_match_all('/--color-([\w]+):/', $themeBlock[1], $cm);
                $themeColorNames = array_values(array_unique($cm[1]));
            }
        }
    } catch (\Throwable) {}
}
@endphp
<div wire:key="field-{{ str_replace(':', '-', $field['slug']) }}-{{ $field['key'] }}" data-field-key="{{ $field['key'] }}" x-show="{{ $fieldShow }}">
    @if ($field['type'] === 'classes')
        <div class="flex items-center justify-between mb-1.5">
            <flux:label class="text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
            <div class="flex items-center gap-2">
                <div class="relative" x-data="{
                    open: false,
                    fieldKey: @js($field['key']),
                    insert(cls) {
                        const ta = document.querySelector('[data-classes-key=\'' + this.fieldKey + '\']');
                        if (ta) {
                            const v = ta.value.trimEnd();
                            ta.value = v ? v + ' ' + cls : cls;
                            ta.dispatchEvent(new Event('input', { bubbles: true }));
                            ta.focus();
                        }
                        this.open = false;
                    }
                }">
                    <flux:tooltip content="Insert theme token" position="bottom">
                        <button type="button"
                            @click="open = !open"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="bolt" class="size-3.5" /></button>
                    </flux:tooltip>
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-75"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 top-full mt-1 z-50 w-56 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg overflow-hidden text-left"
                    >
                        @if (! empty($themeColorNames))
                            <div class="px-3 pt-2.5 pb-2">
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Colors</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($themeColorNames as $colorName)
                                        <button type="button" @click="insert('bg-{{ $colorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">bg-{{ $colorName }}</button>
                                        <button type="button" @click="insert('text-{{ $colorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">text-{{ $colorName }}</button>
                                        <button type="button" @click="insert('border-{{ $colorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">border-{{ $colorName }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="border-t border-zinc-100 dark:border-zinc-700/60"></div>
                        @endif
                        <div class="px-3 pt-2 pb-2">
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Spacing</p>
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="insert('py-section')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section</button>
                                <button type="button" @click="insert('py-section-banner')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section-banner</button>
                                <button type="button" @click="insert('py-section-hero')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section-hero</button>
                            </div>
                        </div>
                        <div class="border-t border-zinc-100 dark:border-zinc-700/60"></div>
                        <div class="px-3 pt-2 pb-2.5">
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Utilities</p>
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="insert('rounded-card')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">rounded-card</button>
                                <button type="button" @click="insert('shadow-card')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">shadow-card</button>
                                <button type="button" @click="insert('font-heading')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">font-heading</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($showAiBtn)
                    <flux:tooltip content="Generate with AI" position="bottom">
                        <button type="button"
                            onclick="(function() { var ta = document.querySelector('[data-classes-key=\'{{ $field['key'] }}\']'); window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: 'classes', fieldLabel: '{{ addslashes($field['label']) }}', currentClasses: ta ? ta.value : '' }, bubbles: true })); })()"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                <flux:tooltip content="Reset" position="bottom">
                    <button wire:click="resetClassesField('{{ $field['key'] }}')" type="button" class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors"><flux:icon name="arrow-path" class="size-3.5" /></button>
                </flux:tooltip>
            </div>
        </div>
    @elseif (! in_array($field['type'], ['toggle', 'note']))
        <div class="flex items-center justify-between mb-1.5">
            <flux:label class="text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
            <div class="flex items-center gap-2">
                @if ($showShortcodeBtn || $field['type'] === 'richtext')
                    <flux:tooltip content="Insert shortcode" position="bottom">
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-shortcode-picker', { detail: { fieldKey: '{{ $field['key'] }}', pageTypeSlug: '{{ $pageTypeSlug }}' }, bubbles: true }))"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="bolt" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($showLoremBtn)
                    <flux:tooltip content="Insert dummy text" position="bottom">
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-lorem-ipsum', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: '{{ $field['type'] }}', defaultSize: '{{ $loremDefaultSize }}' }, bubbles: true }))"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="document-text" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($showAiRewriteBtn)
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <flux:tooltip content="Rewrite with AI" position="bottom">
                            <button
                                type="button"
                                @click="open = !open"
                                class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                            ><flux:icon name="light-bulb" class="size-3.5" /></button>
                        </flux:tooltip>
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 top-full mt-1 z-20 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg py-1 min-w-37"
                        >
                            <p class="px-3 pt-1 pb-0.5 text-[10px] font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wide">Rewrite as</p>
                            @foreach (['proof' => 'Proof Only', 'professional' => 'Professional', 'casual' => 'Casual', 'playful' => 'Playful'] as $toneKey => $toneLabel)
                                <button
                                    type="button"
                                    @click="open = false; window.dispatchEvent(new CustomEvent('open-ai-rewrite', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: '{{ $field['type'] }}', fieldLabel: '{{ addslashes($field['label']) }}', tone: '{{ $toneKey }}', toneLabel: '{{ $toneLabel }}' }, bubbles: true }))"
                                    class="w-full text-left px-3 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors"
                                >{{ $toneLabel }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($showAiBtn)
                    <flux:tooltip content="Generate with AI" position="bottom">
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: '{{ $field['type'] }}', fieldLabel: '{{ addslashes($field['label']) }}' }, bubbles: true }))"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($showAiAltBtn)
                    <flux:tooltip content="Generate alt text from image" position="bottom">
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: 'alt', fieldLabel: '{{ addslashes($field['label']) }}', imageFieldKey: '{{ substr($field['key'], 0, -4) }}' }, bubbles: true }))"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($showAiImageBtn)
                    <flux:tooltip content="Generate image with AI" position="bottom">
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: 'image', fieldLabel: '{{ addslashes($field['label']) }}', rowSlug: '{{ $field['slug'] }}' }, bubbles: true }))"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($showTranslateBtn)
                    <flux:tooltip content="Translate from English with AI" position="bottom">
                        <button type="button"
                            wire:click="translateField('{{ $field['key'] }}', 'en', '{{ $fieldLangCode }}')"
                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                        ><flux:icon name="language" class="size-3.5" /></button>
                    </flux:tooltip>
                @endif
                @if ($field['type'] === 'grid')
                    <button wire:click="clearGridItems('{{ $field['key'] }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-red-500 dark:hover:text-red-400 transition-colors">Remove All</button>
                @endif
                <flux:tooltip content="Reset" position="bottom">
                    <button wire:click="resetContentField('{{ $field['key'] }}')" type="button" class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors"><flux:icon name="arrow-path" class="size-3.5" /></button>
                </flux:tooltip>
            </div>
        </div>
    @endif

    @if ($field['type'] === 'image')
        @php
            $currentPath = $contentValues[$field['key']] ?? '';
            $fallbackToken = $field['fallback_url'] ?? '';
            $fallbackUrl = match($fallbackToken) {
                '__branding_logo__' => \App\Models\Setting::get('branding.logo_url', ''),
                '' => '',
                default => $fallbackToken,
            };
        @endphp
        @if ($currentPath)
            <div class="mb-2 relative inline-block">
                <img
                    src="{{ Storage::url($currentPath) }}"
                    alt=""
                    class="h-24 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700"
                >
                <flux:tooltip content="Remove image">
                    <button
                        wire:click="removeImage('{{ $field['key'] }}')"
                        class="absolute -top-2 -right-2 size-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                    >
                        <flux:icon name="x-mark" class="size-3" />
                    </button>
                </flux:tooltip>
            </div>
        @elseif ($fallbackUrl)
            <div class="mb-2">
                <img
                    src="{{ $fallbackUrl }}"
                    alt=""
                    class="h-12 rounded-lg object-contain border border-zinc-200 dark:border-zinc-700"
                >
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">(Use Branding logo or media library)</p>
            </div>
        @endif
        <button
            wire:click="openMediaPicker('{{ $field['key'] }}')"
            type="button"
            class="flex items-center gap-3 px-4 py-3 w-full border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:border-primary transition-colors"
        >
            <flux:icon name="photo" class="size-5 text-zinc-400 shrink-0" />
            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $currentPath ? 'Replace image…' : 'Pick from Media Library…' }}
            </span>
        </button>
    @elseif ($field['type'] === 'richtext')
        <div wire:ignore
             x-data="richEditor(@js($contentValues[$field['key']] ?? $field['default'] ?? ''), 'contentValues.{{ $field['key'] }}')"
             x-on:shortcode-picked.window="if ($event.detail.fieldKey === '{{ $field['key'] }}') cmd().insertContent($event.detail.shortcode).run()"
             x-on:ai-content-generated.window="if ($event.detail.fieldKey === '{{ $field['key'] }}') { cmd().selectAll().insertContent($event.detail.content).run(); $nextTick(() => cmd().focus().run()); }"
             x-on:content-richtext-reset.window="if ($event.detail.key === '{{ $field['key'] }}') cmd().setContent($event.detail.value).run()"
             @keydown.ctrl.z="$wire.popContentHistory()"
             @keydown.meta.z="$wire.popContentHistory()"
             class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            {{-- Compact toolbar --}}
            <div class="flex flex-wrap items-center gap-0.5 border-b border-zinc-200 dark:border-zinc-700 px-1.5 py-1">
                <button type="button" @click="cmd().toggleBold().run()" :class="active.bold ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs font-bold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Bold">B</button>
                <button type="button" @click="cmd().toggleItalic().run()" :class="active.italic ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs italic text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Italic">I</button>
                <button type="button" @click="cmd().toggleUnderline().run()" :class="active.underline ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs underline text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Underline">U</button>
                <button type="button" @click="cmd().toggleStrike().run()" :class="active.strike ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs line-through text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Strikethrough">S</button>
                <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-600 mx-0.5"></div>
                <button type="button" @click="cmd().toggleBulletList().run()" :class="active.bulletList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Bullet List">• List</button>
                <button type="button" @click="cmd().toggleOrderedList().run()" :class="active.orderedList ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Ordered List">1. List</button>
                <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-600 mx-0.5"></div>
                <button type="button" @click="setLink()" :class="active.link ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="rounded px-1.5 py-0.5 text-xs text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Link">Link</button>
                <button type="button" @click="cmd().unsetAllMarks().clearNodes().run()" class="rounded px-1.5 py-0.5 text-xs text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="Clear Formatting">✕</button>
                <button type="button" @click="toggleSource()" :class="sourceMode ? 'bg-zinc-200 dark:bg-zinc-600' : ''" class="ml-auto rounded px-1.5 py-0.5 text-xs font-mono text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" title="HTML Source">&lt;/&gt;</button>
            </div>
            <div x-ref="editorEl" class="sidebar-rich-editor-content" x-show="!sourceMode"></div>
            <textarea x-show="sourceMode" x-model="sourceHtml" rows="5" class="w-full p-3 font-mono text-xs text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-900 outline-none resize-y border-0"></textarea>
        </div>
    @elseif ($field['type'] === 'classes')
        <div x-data="twAutocomplete('{{ $field['key'] }}')" class="relative"
            x-on:ai-content-generated.window="
                if ($event.detail.fieldKey === '{{ $field['key'] }}') {
                    $refs.input.value = $event.detail.content;
                    $refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $refs.input.focus();
                }
            ">
            <textarea
                x-ref="input"
                data-classes-key="{{ $field['key'] }}"
                wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                rows="3"
                x-on:input="suggest($event)"
                x-on:keydown="handleKey($event)"
                x-on:blur="delayClose()"
                @mousedown.stop
                placeholder="{{ $field['default'] }}"
                class="w-full font-mono text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
            ></textarea>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-75"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-bind:style="dropdownStyle"
                class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg overflow-y-auto max-h-48"
            >
                <template x-for="(item, i) in suggestions" :key="item">
                    <button
                        type="button"
                        @mousedown.prevent="pick(item)"
                        :class="i === activeIndex ? 'bg-primary text-white' : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700'"
                        class="block w-full text-left px-3 py-1.5 text-xs font-mono transition-colors"
                        x-text="item"
                    ></button>
                </template>
            </div>
            <div class="flex items-center gap-1.5 mt-1">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Tailwind CSS classes. Tab or Enter to complete.</p>
                <flux:tooltip content="Tailwind CSS reference" position="right">
                    <button
                        @click="$flux.modal('tailwind-css-help').show()"
                        type="button"
                        class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors shrink-0"
                    >
                        <flux:icon name="question-mark-circle" class="size-3.5" />
                    </button>
                </flux:tooltip>
            </div>
        </div>
    @elseif ($field['type'] === 'grid')
        @php
            $gridRaw = $contentValues[$field['key']] ?? $field['default'];
            $gridItems = json_decode($gridRaw, true) ?: [];
            $gridDefaultItems = json_decode($field['default'], true) ?: [];
            $gridKeys = !empty($gridItems) ? array_keys($gridItems[0]) : (!empty($gridDefaultItems) ? array_keys($gridDefaultItems[0]) : []);

            // Expand keys with language variant suffixes for translatable sub-fields.
            $gridLangs = array_values(array_filter(
                \App\Models\Setting::get('site.languages', [['code' => 'en']]),
                fn ($l) => $l['code'] !== 'en'
            ));
            $gridNonTranslatableKey = function (string $k): bool {
                if (in_array($k, ['icon', 'image', 'alt', 'url'], true)) {
                    return true;
                }
                if (str_starts_with($k, 'toggle_')) {
                    return true;
                }
                if (str_ends_with($k, '_image') || str_ends_with($k, '_alt') || str_ends_with($k, '_url')) {
                    return true;
                }
                return false;
            };
            $expandedGridKeys = [];
            foreach ($gridKeys as $gk) {
                // Skip existing lang variant keys (they'll be expanded from base keys).
                if (str_contains($gk, '__')) {
                    continue;
                }
                $expandedGridKeys[] = $gk;
                if (! $gridNonTranslatableKey($gk)) {
                    foreach ($gridLangs as $gl) {
                        $expandedGridKeys[] = $gk . '__' . $gl['code'];
                    }
                }
            }
        @endphp
        <div
            wire:ignore
            x-data="{
                items: {{ json_encode($gridItems) }},
                keys: {{ json_encode($expandedGridKeys) }},
                openItems: {},
                generatingAlt: {},
                sync() {
                    $wire.set('contentValues.{{ $field['key'] }}', JSON.stringify(this.items));
                },
                updateField(idx, fKey, val) {
                    this.items[idx][fKey] = val;
                    this.sync();
                },
                removeItem(idx) {
                    this.items.splice(idx, 1);
                    this.sync();
                },
                addItem() {
                    this.items.push(Object.fromEntries(this.keys.map(k => [k, ''])));
                    this.sync();
                },
                generateAlt(idx, fKey) {
                    const imageKey = fKey.replace(/_alt$/, '');
                    const imagePath = this.items[idx][imageKey] || '';
                    if (!imagePath) { alert('No image found. Please upload an image first.'); return; }
                    this.generatingAlt = { ...this.generatingAlt, [idx + '-' + fKey]: true };
                    $wire.call('generateAiGridItemAltText', '{{ $field['key'] }}', idx, fKey, imagePath);
                },
                keyLabel(fKey) {
                    const parts = fKey.split('__');
                    if (parts.length === 2 && parts[1].length >= 2 && parts[1].length <= 10) {
                        return parts[0].replace(/_/g, ' ') + ' (' + parts[1].toUpperCase() + ')';
                    }
                    return fKey.replace(/_/g, ' ');
                },
                isTextareaKey(fKey) {
                    const base = fKey.split('__')[0];
                    return base === 'desc' || base === 'description' || base === 'answer' || base === 'a';
                }
            }"
            x-on:content-grid-reset.window="if ($event.detail.key === '{{ $field['key'] }}') {
                const parsed = JSON.parse($event.detail.value || '[]');
                items = parsed;
                keys = parsed.length > 0 ? Object.keys(parsed[0]) : keys;
            }"
            x-on:ai-grid-item-alt-generated.window="if ($event.detail.gridKey === '{{ $field['key'] }}') {
                updateField($event.detail.idx, $event.detail.altKey, $event.detail.content);
                generatingAlt = { ...generatingAlt, [$event.detail.idx + '-' + $event.detail.altKey]: false };
            }"
            x-on:ai-grid-item-image-generated.window="if ($event.detail.gridKey === '{{ $field['key'] }}') {
                updateField($event.detail.idx, $event.detail.itemKey, $event.detail.path);
            }"
            x-on:ai-grid-alt-error.window="if ($event.detail.gridKey === '{{ $field['key'] }}') {
                alert('AI error: ' + $event.detail.message);
                generatingAlt = { ...generatingAlt, [$event.detail.idx + '-' + $event.detail.altKey]: false };
            }"
            class="space-y-2"
        >
            <template x-for="(item, idx) in items" :key="idx">
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    {{-- Collapsed header --}}
                    <div class="flex items-center gap-1.5 px-3 py-2">
                        <button type="button" @click="const wasOpen = openItems[idx]; openItems = {}; openItems[idx] = !wasOpen" class="flex items-center gap-1.5 flex-1 min-w-0 text-left">
                            <flux:icon name="chevron-right" class="size-4 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openItems[idx] ? 'rotate-90' : ''" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-200 font-medium truncate" x-text="item.title || item.name || item.label || item.alt || (item.image ? item.image.split('/').pop() : null) || ('Item ' + (idx + 1))"></span>
                        </button>
                        <flux:tooltip content="Remove item">
                            <button
                                type="button"
                                @click="removeItem(idx)"
                                class="text-zinc-400 hover:text-red-500 transition-colors shrink-0"
                            >
                                <flux:icon name="x-mark" class="size-3.5" />
                            </button>
                        </flux:tooltip>
                    </div>
                    {{-- Expanded fields --}}
                    <div x-show="openItems[idx]" x-transition class="border-t border-zinc-200 dark:border-zinc-700 px-3 pt-2 pb-3 space-y-2">
                    <template x-for="fKey in keys" :key="fKey">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400" x-text="keyLabel(fKey)"></p>
                                @if ($aiEnabled)
                                    <flux:tooltip content="Generate alt text from image" position="bottom">
                                        <button
                                            type="button"
                                            x-show="fKey.endsWith('_alt') || fKey === 'alt'"
                                            @click="generateAlt(idx, fKey)"
                                            :class="generatingAlt[idx + '-' + fKey] ? 'opacity-50 cursor-not-allowed' : 'hover:text-primary dark:hover:text-primary'"
                                            class="text-zinc-400 dark:text-zinc-500 transition-colors shrink-0"
                                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                                    </flux:tooltip>
                                @endif
                                @if ($aiImageEnabled)
                                    <flux:tooltip content="Generate with AI" position="bottom">
                                        <button
                                            type="button"
                                            x-show="fKey === 'image' || fKey.endsWith('_image')"
                                            @click="window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $field['key'] }}', fieldType: 'image', fieldLabel: 'Card Image', rowSlug: '{{ $field['slug'] }}', gridItemIdx: idx, gridItemKey: fKey }, bubbles: true }))"
                                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors shrink-0"
                                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                                    </flux:tooltip>
                                @endif
                            </div>
                            <template x-if="fKey === 'icon'">
                                <div x-data="{ pickerOpen: false, search: '', variant: 'outline' }">
                                    {{-- Compact current selection --}}
                                    <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div class="size-5 shrink-0 text-zinc-600 dark:text-zinc-300">
                                            @foreach (['bolt', 'rocket-launch', 'shield-check', 'lock-closed', 'key', 'chart-bar', 'chart-pie', 'arrow-trending-up', 'chat-bubble-left-right', 'envelope', 'bell', 'cog-6-tooth', 'adjustments-horizontal', 'wrench-screwdriver', 'globe-alt', 'server', 'cloud', 'light-bulb', 'sparkles', 'star', 'puzzle-piece', 'link', 'user-group', 'device-phone-mobile'] as $iconName)
                                                <template x-if="item[fKey] === '{{ $iconName }}'">
                                                    <x-heroicon name="{{ $iconName }}" class="size-5" />
                                                </template>
                                                <template x-if="item[fKey] === '{{ $iconName }}:solid'">
                                                    <x-heroicon name="{{ $iconName }}" variant="solid" class="size-5" />
                                                </template>
                                            @endforeach
                                        </div>
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400 flex-1 font-mono truncate" x-text="item[fKey] || '—'"></span>
                                        <button type="button" @click="pickerOpen = true" class="text-xs text-primary hover:text-primary/80 shrink-0 transition-colors">
                                            Change
                                        </button>
                                    </div>

                                    {{-- Modal overlay --}}
                                    @php
                                        if (! isset($outlineIcons)) {
                                            $allIconsData = require resource_path('heroicons/data.php');
                                            $outlineIcons = array_keys($allIconsData['outline']);
                                            $solidIcons   = array_keys($allIconsData['solid']);
                                        }
                                    @endphp
                                    <div
                                        x-show="pickerOpen"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-6"
                                    >
                                        <div class="absolute inset-0 bg-black/50" @click="pickerOpen = false; search = ''"></div>
                                        <div class="relative z-10 bg-white dark:bg-zinc-800 rounded-xl shadow-2xl flex flex-col w-full max-w-2xl max-h-[80vh]">
                                            {{-- Header --}}
                                            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">Select Icon</p>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 p-0.5">
                                                        <button type="button" @click="variant = 'outline'" :class="variant === 'outline' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Outline</button>
                                                        <button type="button" @click="variant = 'solid'" :class="variant === 'solid' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Solid</button>
                                                    </div>
                                                    <button type="button" @click="pickerOpen = false; search = ''" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                                                        <flux:icon name="x-mark" class="size-4" />
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- Search --}}
                                            <div class="px-5 py-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                                                <input
                                                    x-model="search"
                                                    type="text"
                                                    placeholder="Search icons… or press Enter to use any name"
                                                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                    @keydown.enter.prevent="if (search.trim()) { updateField(idx, fKey, search.trim()); search = ''; pickerOpen = false; }"
                                                />
                                            </div>
                                            {{-- Icon grids --}}
                                            <div class="overflow-y-auto p-5">
                                                <div x-show="variant === 'outline'" class="grid grid-cols-10 gap-1">
                                                    @foreach ($outlineIcons as $iconName)
                                                        <button type="button" x-show="!search || '{{ $iconName }}'.includes(search)" @click="updateField(idx, fKey, '{{ $iconName }}'); search = ''; pickerOpen = false" :class="item[fKey] === '{{ $iconName }}' ? 'border-primary bg-primary/10 text-primary' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400'" class="flex items-center justify-center p-2 rounded-lg border transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" class="size-5" /></button>
                                                    @endforeach
                                                </div>
                                                <div x-show="variant === 'solid'" class="grid grid-cols-10 gap-1">
                                                    @foreach ($solidIcons as $iconName)
                                                        <button type="button" x-show="!search || '{{ $iconName }}'.includes(search)" @click="updateField(idx, fKey, '{{ $iconName }}:solid'); search = ''; pickerOpen = false" :class="item[fKey] === '{{ $iconName }}:solid' ? 'border-primary bg-primary/10 text-primary' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400'" class="flex items-center justify-center p-2 rounded-lg border transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" variant="solid" class="size-5" /></button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="isTextareaKey(fKey)">
                                <textarea
                                    :value="item[fKey]"
                                    @change="updateField(idx, fKey, $event.target.value)"
                                    rows="3"
                                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"
                                ></textarea>
                            </template>
                            <template x-if="fKey === 'image' || fKey.endsWith('_image')">
                                <div>
                                    <div x-show="item[fKey]" class="mb-2">
                                        <img :src="item[fKey] ? '/storage/' + item[fKey] : ''"
                                            class="h-16 w-24 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700" />
                                    </div>
                                    <button
                                        type="button"
                                        @click="$wire.call('openGridItemMediaPicker', '{{ $field['key'] }}', idx, fKey)"
                                        class="flex items-center gap-2 px-3 py-2 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors w-full"
                                    >
                                        <flux:icon name="photo" class="size-4 shrink-0" />
                                        <span x-text="item[fKey] ? 'Change image…' : 'Pick from library…'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="fKey.endsWith('_alt') || fKey === 'alt'">
                                <input
                                    :value="item[fKey]"
                                    @change="updateField(idx, fKey, $event.target.value)"
                                    type="text"
                                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                />
                            </template>
                            <template x-if="fKey !== 'icon' && !isTextareaKey(fKey) && fKey !== 'image' && !fKey.endsWith('_image') && !fKey.endsWith('_alt') && fKey !== 'alt'">
                                <input
                                    :value="item[fKey]"
                                    @change="updateField(idx, fKey, $event.target.value)"
                                    type="text"
                                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                />
                            </template>
                        </div>
                    </template>
                    </div>{{-- /x-show expanded fields --}}
                </div>
            </template>
            <button
                type="button"
                @click="addItem"
                class="w-full py-2 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors"
            >
                + Add Item
            </button>
            @if (in_array('image', $gridKeys))
                <button
                    type="button"
                    wire:click="openGalleryPicker('{{ $field['key'] }}')"
                    class="w-full py-2 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors"
                >
                    + Add images from library
                </button>
            @endif
        </div>
    @elseif ($field['type'] === 'toggle')
        <div class="flex items-center gap-2">
            <flux:switch wire:model.live="contentValues.{{ $field['key'] }}" />
            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</span>
            <flux:tooltip content="Reset" position="bottom">
                <button wire:click="resetContentField('{{ $field['key'] }}')" type="button" class="ml-auto text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors"><flux:icon name="arrow-path" class="size-3.5" /></button>
            </flux:tooltip>
        </div>
    @elseif (str_ends_with($field['key'], '_htag'))
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="h1">h1</flux:select.option>
            <flux:select.option value="h2">h2</flux:select.option>
            <flux:select.option value="h3">h3</flux:select.option>
            <flux:select.option value="h4">h4</flux:select.option>
        </flux:select>
    @elseif (str_ends_with($field['key'], '_url'))
        <flux:input
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            type="url"
            placeholder="{{ $field['default'] ?: 'https://' }}"
        />
    @elseif ($field['key'] === 'subheadline')
        <div
            @keydown.ctrl.z.prevent="$wire.undo()"
            @keydown.meta.z.prevent="$wire.undo()"
            x-on:shortcode-picked.window="
                if ($event.detail.fieldKey === '{{ $field['key'] }}') {
                    const el = $el.querySelector('textarea');
                    const sc = $event.detail.shortcode;
                    const start = el.selectionStart;
                    const end = el.selectionEnd;
                    const val = el.value;
                    el.value = val.slice(0, start) + sc + val.slice(end);
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.focus();
                    el.setSelectionRange(start + sc.length, start + sc.length);
                }
            "
            x-on:ai-content-generated.window="
                if ($event.detail.fieldKey === '{{ $field['key'] }}') {
                    const el = $el.querySelector('textarea');
                    el.value = $event.detail.content;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.focus();
                }
            ">
            <flux:textarea
                wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                rows="3"
                placeholder="{{ $field['default'] }}"
            />
        </div>
    @elseif ($field['type'] === 'id')
        <flux:input
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            placeholder="e.g. section-hero"
        />
        <flux:text class="text-xs text-zinc-400 mt-1">Used for anchor links: <code class="font-mono">#section-hero</code>. No spaces.</flux:text>
    @elseif ($field['type'] === 'form_select')
        @php $availableForms = \App\Models\Form::query()->orderBy('name')->get(['id', 'name']); @endphp
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">Select a form…</flux:select.option>
            @foreach ($availableForms as $availableForm)
                <flux:select.option value="{{ $availableForm->id }}">{{ $availableForm->name }}</flux:select.option>
            @endforeach
        </flux:select>
    @elseif (str_ends_with($field['key'], '_menu'))
        @php $availableMenus = \App\Models\Setting::get('navigation.menus', []); @endphp
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            @foreach ($availableMenus as $availableMenu)
                <flux:select.option value="{{ $availableMenu['slug'] }}">{{ $availableMenu['label'] }}</flux:select.option>
            @endforeach
        </flux:select>
    @elseif ($field['type'] === 'object_fit')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— default —</flux:select.option>
            <flux:select.option value="cover">Cover — crop to fill</flux:select.option>
            <flux:select.option value="contain">Contain — show whole image</flux:select.option>
            <flux:select.option value="fill">Fill — stretch to fit</flux:select.option>
            <flux:select.option value="none">None — original size</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'border_radius')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— default —</flux:select.option>
            <flux:select.option value="rounded-sm">Slight rounding (sm)</flux:select.option>
            <flux:select.option value="rounded">Default rounding</flux:select.option>
            <flux:select.option value="rounded-md">Medium rounding (md)</flux:select.option>
            <flux:select.option value="rounded-lg">Large rounding (lg)</flux:select.option>
            <flux:select.option value="rounded-xl">Extra large rounding (xl)</flux:select.option>
            <flux:select.option value="rounded-2xl">Very rounded (2xl)</flux:select.option>
            <flux:select.option value="rounded-3xl">Very very rounded (3xl)</flux:select.option>
            <flux:select.option value="rounded-full">Perfect circle if image is square (Full)</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'animation')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— None —</flux:select.option>
            <flux:select.option value="fade-up">Fade Up</flux:select.option>
            <flux:select.option value="fade-down">Fade Down</flux:select.option>
            <flux:select.option value="fade-left">Fade Left</flux:select.option>
            <flux:select.option value="fade-right">Fade Right</flux:select.option>
            <flux:select.option value="zoom-in">Zoom In</flux:select.option>
            <flux:select.option value="fade">Fade</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'animation_delay')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— None —</flux:select.option>
            <flux:select.option value="delay-100">100ms</flux:select.option>
            <flux:select.option value="delay-200">200ms</flux:select.option>
            <flux:select.option value="delay-300">300ms</flux:select.option>
            <flux:select.option value="delay-500">500ms</flux:select.option>
            <flux:select.option value="delay-700">700ms</flux:select.option>
            <flux:select.option value="delay-1000">1000ms</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'bg_position')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— default —</flux:select.option>
            <flux:select.option value="center">Center</flux:select.option>
            <flux:select.option value="top">Top</flux:select.option>
            <flux:select.option value="bottom">Bottom</flux:select.option>
            <flux:select.option value="left">Left</flux:select.option>
            <flux:select.option value="right">Right</flux:select.option>
            <flux:select.option value="left-top">Top Left</flux:select.option>
            <flux:select.option value="left-bottom">Bottom Left</flux:select.option>
            <flux:select.option value="right-top">Top Right</flux:select.option>
            <flux:select.option value="right-bottom">Bottom Right</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'bg_size')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— default —</flux:select.option>
            <flux:select.option value="cover">Cover — scale to fill</flux:select.option>
            <flux:select.option value="contain">Contain — show whole image</flux:select.option>
            <flux:select.option value="auto">Auto — original size</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'bg_repeat')
        <flux:select wire:model.live="contentValues.{{ $field['key'] }}">
            <flux:select.option value="">— default —</flux:select.option>
            <flux:select.option value="no-repeat">No Repeat</flux:select.option>
            <flux:select.option value="repeat">Repeat (tile)</flux:select.option>
            <flux:select.option value="repeat-x">Repeat Horizontally</flux:select.option>
            <flux:select.option value="repeat-y">Repeat Vertically</flux:select.option>
        </flux:select>
    @elseif ($field['type'] === 'note')
        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-3 py-2.5 text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">{!! $field['message'] ?? '' !!}</div>
    @elseif ($field['type'] === 'attrs')
        @php
            $attrsRaw = $contentValues[$field['key']] ?? $field['default'];
            $attrsItems = json_decode($attrsRaw ?: '[]', true) ?: [];
        @endphp
        <div
            wire:ignore
            x-data="{
                items: {{ json_encode($attrsItems) }},
                openItems: {},
                sync() {
                    $wire.set('contentValues.{{ $field['key'] }}', JSON.stringify(this.items));
                },
                updateItem(idx, key, val) {
                    this.items[idx][key] = val;
                    this.sync();
                },
                removeItem(idx) {
                    this.items.splice(idx, 1);
                    this.sync();
                },
                addItem() {
                    this.items.push({ name: '', value: '' });
                    this.sync();
                }
            }"
            x-on:content-attrs-reset.window="if ($event.detail.key === '{{ $field['key'] }}') { items = JSON.parse($event.detail.value || '[]'); }"
            class="space-y-2"
        >
            <template x-for="(item, idx) in items" :key="idx">
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="flex items-center gap-1.5 px-3 py-2">
                        <button type="button" @click="openItems[idx] = !openItems[idx]" class="flex items-center gap-1.5 flex-1 min-w-0 text-left">
                            <flux:icon name="chevron-right" class="size-4 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openItems[idx] ? 'rotate-90' : ''" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-200 font-medium font-mono truncate" x-text="item.name || ('Attribute ' + (idx + 1))"></span>
                        </button>
                        <flux:tooltip content="Remove attribute">
                            <button
                                type="button"
                                @click="removeItem(idx)"
                                class="text-zinc-400 hover:text-red-500 transition-colors shrink-0"
                            >
                                <flux:icon name="x-mark" class="size-3.5" />
                            </button>
                        </flux:tooltip>
                    </div>
                    <div x-show="openItems[idx]" x-transition class="border-t border-zinc-200 dark:border-zinc-700 px-3 pt-2 pb-3 space-y-2">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Name</p>
                            <input
                                :value="item.name"
                                @change="updateItem(idx, 'name', $event.target.value)"
                                type="text"
                                placeholder="data-section"
                                class="w-full text-sm font-mono rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Value</p>
                            <input
                                :value="item.value"
                                @change="updateItem(idx, 'value', $event.target.value)"
                                type="text"
                                placeholder="my-value"
                                class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            />
                        </div>
                    </div>
                </div>
            </template>
            <button
                type="button"
                @click="addItem"
                class="w-full py-2 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors"
            >
                + Add Attribute
            </button>
        </div>
    @else
        <div
            @keydown.ctrl.z.prevent="$wire.undo()"
            @keydown.meta.z.prevent="$wire.undo()"
            x-on:shortcode-picked.window="
                if ($event.detail.fieldKey === '{{ $field['key'] }}') {
                    const el = $el.querySelector('input');
                    const sc = $event.detail.shortcode;
                    const start = el.selectionStart;
                    const end = el.selectionEnd;
                    const val = el.value;
                    el.value = val.slice(0, start) + sc + val.slice(end);
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.focus();
                    el.setSelectionRange(start + sc.length, start + sc.length);
                }
            "
            x-on:ai-content-generated.window="
                if ($event.detail.fieldKey === '{{ $field['key'] }}') {
                    const el = $el.querySelector('input');
                    el.value = $event.detail.content;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.focus();
                }
            ">
            <flux:input
                wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                placeholder="{{ $field['default'] }}"
            />
        </div>
    @endif
</div>

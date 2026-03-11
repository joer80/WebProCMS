@php
    use App\Models\ContentTypeDefinition;
    use App\Models\Shortcode;
    use App\Support\SystemShortcodes;

    $systemShortcodes = SystemShortcodes::all();
    $userShortcodes = Shortcode::query()->where('is_active', true)->orderBy('tag')->get();
    $contentTypes = ContentTypeDefinition::allOrdered();
@endphp

<div
    x-data="{
        open: false,
        targetFieldKey: null,
        sections: { system: true, user: true, types: {} },
        pick(tag) {
            window.dispatchEvent(new CustomEvent('shortcode-picked', { detail: { fieldKey: this.targetFieldKey, shortcode: tag }, bubbles: true }));
            this.open = false;
            this.targetFieldKey = null;
        }
    }"
    @open-shortcode-picker.window="open = true; targetFieldKey = $event.detail.fieldKey"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4"
    @keydown.escape.window="open = false"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700 flex flex-col max-h-[80vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Insert Shortcode</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Click a shortcode to insert it into the field.</p>
            </div>
            <button type="button" @click="open = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>

        {{-- Scrollable content --}}
        <div class="overflow-y-auto flex-1 divide-y divide-zinc-100 dark:divide-zinc-800">

            {{-- System shortcodes --}}
            <div>
                <button type="button"
                    @click="sections.system = !sections.system"
                    class="w-full flex items-center justify-between px-5 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">System</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-150" :class="sections.system ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="sections.system" class="pb-2">
                    @foreach ($systemShortcodes as $tag => $info)
                        @php $shortcode = '[[' . $tag . ']]'; @endphp
                        <button type="button"
                            @click="pick('{{ $shortcode }}')"
                            class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                            <div>
                                <span class="font-mono text-sm text-primary">{{ $shortcode }}</span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $info['label'] }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0-6-6m6 6 6-6" />
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- User shortcodes --}}
            @if ($userShortcodes->isNotEmpty())
                <div>
                    <button type="button"
                        @click="sections.user = !sections.user"
                        class="w-full flex items-center justify-between px-5 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Custom Shortcodes</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-150" :class="sections.user ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="sections.user" class="pb-2">
                        @foreach ($userShortcodes as $shortcode)
                            @php $tag = '[[' . $shortcode->tag . ']]'; @endphp
                            <button type="button"
                                @click="pick('{{ $tag }}')"
                                class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                                <div>
                                    <span class="font-mono text-sm text-primary">{{ $tag }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($shortcode->type) }}</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0-6-6m6 6 6-6" />
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Content type field shortcodes --}}
            @foreach ($contentTypes as $type)
                @if (count($type->fields) > 0)
                    <div>
                        <button type="button"
                            @click="sections.types['{{ $type->slug }}'] = !(sections.types['{{ $type->slug }}'] ?? true)"
                            class="w-full flex items-center justify-between px-5 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-center gap-2">
                                <flux:icon name="{{ $type->icon }}" class="size-4 text-zinc-400" />
                                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ $type->name }} Fields</span>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">(on {{ $type->slug }}/show page)</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 transition-transform duration-150" :class="(sections.types['{{ $type->slug }}'] ?? true) ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                        </button>
                        <div x-show="sections.types['{{ $type->slug }}'] ?? true" class="pb-2">
                            @foreach ($type->fields as $field)
                                @php $tag = '[[field:' . $field['name'] . ']]'; @endphp
                                <button type="button"
                                    @click="pick('{{ $tag }}')"
                                    class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                                    <div>
                                        <span class="font-mono text-sm text-primary">{{ $tag }}</span>
                                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $field['label'] }} &middot; {{ $field['type'] }}</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0-6-6m6 6 6-6" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>

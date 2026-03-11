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
        copied: null,
        sections: { system: true, user: true, types: {} },
        copy(tag) {
            navigator.clipboard.writeText(tag).then(() => {
                this.copied = tag;
                setTimeout(() => { this.copied = null; }, 1500);
            });
        }
    }"
    @open-shortcode-modal.window="open = true"
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
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Shortcode Reference</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Click any shortcode to copy it, then paste into any text or richtext field.</p>
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
                            @click="copy('{{ $shortcode }}')"
                            class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                            <div>
                                <span class="font-mono text-sm text-primary">{{ $shortcode }}</span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $info['label'] }}</span>
                            </div>
                            <span x-show="copied === '{{ $shortcode }}'" class="text-xs text-green-600 dark:text-green-400 shrink-0">Copied!</span>
                            <svg x-show="copied !== '{{ $shortcode }}'" xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" /></svg>
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
                                @click="copy('{{ $tag }}')"
                                class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                                <div>
                                    <span class="font-mono text-sm text-primary">{{ $tag }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($shortcode->type) }}</span>
                                </div>
                                <span x-show="copied === '{{ $tag }}'" class="text-xs text-green-600 dark:text-green-400 shrink-0">Copied!</span>
                                <svg x-show="copied !== '{{ $tag }}'" xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" /></svg>
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
                                    @click="copy('{{ $tag }}')"
                                    class="w-full flex items-center justify-between gap-3 px-5 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-left group">
                                    <div>
                                        <span class="font-mono text-sm text-primary">{{ $tag }}</span>
                                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $field['label'] }} &middot; {{ $field['type'] }}</span>
                                    </div>
                                    <span x-show="copied === '{{ $tag }}'" class="text-xs text-green-600 dark:text-green-400 shrink-0">Copied!</span>
                                    <svg x-show="copied !== '{{ $tag }}'" xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" /></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>

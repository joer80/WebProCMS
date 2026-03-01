<div wire:key="field-{{ $field['key'] }}" x-show="{{ $field['type'] === 'classes' ? '(designMode || groupDesignMode) && !groupContentMode' : 'groupContentMode || (!designMode && !groupDesignMode)' }}">
    @if ($field['type'] === 'classes')
        <div class="flex items-center justify-between mb-1.5">
            <flux:label class="text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
            <button wire:click="resetClassesField('{{ $field['key'] }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
        </div>
    @elseif ($field['type'] !== 'toggle')
        <flux:label class="mb-1.5 text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
    @endif

    @if ($field['type'] === 'image')
        @php $currentPath = $contentValues[$field['key']] ?? ''; @endphp
        @if ($currentPath)
            <div class="mb-2 relative inline-block">
                <img
                    src="{{ Storage::url($currentPath) }}"
                    alt=""
                    class="h-24 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700"
                >
                <button
                    wire:click="removeImage('{{ $field['key'] }}')"
                    class="absolute -top-2 -right-2 size-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                    title="Remove image"
                >
                    <flux:icon name="x-mark" class="size-3" />
                </button>
            </div>
        @endif
        <div
            x-data
            x-on:click="$refs.imgInput_{{ $field['key'] }}.click()"
            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:border-primary transition-colors"
        >
            <flux:icon name="photo" class="size-5 text-zinc-400 shrink-0" />
            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $currentPath ? 'Replace image…' : 'Upload image…' }}
            </span>
            <input
                x-ref="imgInput_{{ $field['key'] }}"
                type="file"
                accept="image/*"
                class="hidden"
                x-on:change="
                    $wire.setPendingImageKey('{{ $field['key'] }}').then(() => {
                        $wire.upload('pendingImageUpload', $event.target.files[0])
                    })
                "
            >
        </div>
        <button
            wire:click="openMediaPicker('{{ $field['key'] }}')"
            type="button"
            class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 hover:text-primary dark:hover:text-primary underline"
        >
            or pick from Media Library
        </button>
    @elseif ($field['type'] === 'richtext')
        <flux:textarea
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            rows="4"
            placeholder="{{ $field['default'] }}"
        />
        <flux:text class="text-xs text-zinc-400 mt-1">HTML is supported.</flux:text>
    @elseif ($field['type'] === 'classes')
        <div x-data="twAutocomplete('{{ $field['key'] }}')" class="relative">
            <textarea
                x-ref="input"
                wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                rows="2"
                x-on:input="suggest($event)"
                x-on:keydown="handleKey($event)"
                x-on:blur="delayClose()"
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
            <div x-data="{ showHelp: false }" class="mt-1">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Tailwind CSS classes. Tab or Enter to complete.</p>
                    <button
                        @click="showHelp = !showHelp"
                        type="button"
                        :class="showHelp ? 'text-primary' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300'"
                        class="transition-colors shrink-0"
                        title="Tips"
                    >
                        <flux:icon name="question-mark-circle" class="size-3.5" />
                    </button>
                </div>
                <div
                    x-show="showHelp"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-2 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 text-xs space-y-3"
                >
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Arbitrary values</p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Hardcode any value in square brackets:</p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">text-[1.25rem] w-[320px] mt-[10px]</code>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Responsive prefixes</p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Apply at a breakpoint and up — <span class="font-mono">sm: md: lg: xl:</span></p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">text-sm md:text-lg lg:text-xl</code>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Dark mode</p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Prefix with <span class="font-mono">dark:</span> to apply only in dark mode:</p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">bg-white dark:bg-zinc-900</code>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Hover &amp; state variants</p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Prefix with <span class="font-mono">hover:</span>, <span class="font-mono">focus:</span>, etc.:</p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">hover:opacity-80 hover:scale-105</code>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Force override with <span class="font-mono">!</span></p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Prefix any class with <span class="font-mono">!</span> to mark it important:</p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">!text-center !mt-0</code>
                    </div>
                    <div>
                        <p class="font-medium text-zinc-600 dark:text-zinc-300 mb-0.5">Theme colors</p>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-1">Your brand colors from Branding settings:</p>
                        <code class="block font-mono bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1 rounded">text-primary bg-primary border-primary</code>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($field['type'] === 'toggle')
        <div class="flex items-center gap-2">
            <flux:switch wire:model.live="contentValues.{{ $field['key'] }}" />
            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</span>
        </div>
    @elseif (str_ends_with($field['key'], '_url'))
        <flux:input
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            type="url"
            placeholder="{{ $field['default'] ?: 'https://' }}"
        />
    @elseif ($field['key'] === 'subheadline')
        <flux:textarea
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            rows="3"
            placeholder="{{ $field['default'] }}"
        />
    @else
        <flux:input
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            placeholder="{{ $field['default'] }}"
        />
    @endif
</div>

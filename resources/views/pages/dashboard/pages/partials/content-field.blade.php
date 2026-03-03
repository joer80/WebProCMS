<div wire:key="field-{{ str_replace(':', '-', $field['slug']) }}-{{ $field['key'] }}" x-show="{{ $field['type'] === 'classes' ? '(designMode || groupDesignMode) && !groupContentMode' : 'groupContentMode || !groupHasClasses || (!designMode && !groupDesignMode)' }}">
    @if ($field['type'] === 'classes')
        <div class="flex items-center justify-between mb-1.5">
            <flux:label class="text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
            <button wire:click="resetClassesField('{{ $field['key'] }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
        </div>
    @elseif ($field['type'] !== 'toggle')
        <div class="flex items-center justify-between mb-1.5">
            <flux:label class="text-zinc-500 dark:text-zinc-400">{{ $field['label'] }}</flux:label>
            <div class="flex items-center gap-2">
                @if ($field['type'] === 'grid')
                    <button wire:click="clearGridItems('{{ $field['key'] }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-red-500 dark:hover:text-red-400 transition-colors">Remove All</button>
                @endif
                <button wire:click="resetContentField('{{ $field['key'] }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
            </div>
        </div>
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
                rows="3"
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
    @elseif ($field['type'] === 'grid')
        @php
            $gridRaw = $contentValues[$field['key']] ?? $field['default'];
            $gridItems = json_decode($gridRaw, true) ?: [];
            $gridDefaultItems = json_decode($field['default'], true) ?: [];
            $gridKeys = !empty($gridItems) ? array_keys($gridItems[0]) : (!empty($gridDefaultItems) ? array_keys($gridDefaultItems[0]) : []);
        @endphp
        <div
            x-data="{
                items: {{ json_encode($gridItems) }},
                keys: {{ json_encode($gridKeys) }},
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
                }
            }"
            x-on:content-grid-reset.window="if ($event.detail.key === '{{ $field['key'] }}') {
                const parsed = JSON.parse($event.detail.value || '[]');
                items = parsed;
                keys = parsed.length > 0 ? Object.keys(parsed[0]) : keys;
            }"
            class="space-y-2"
        >
            <template x-for="(item, idx) in items" :key="idx">
                <div x-data="{ open: false }" class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    {{-- Collapsed header --}}
                    <div class="flex items-center gap-1.5 px-3 py-2">
                        <button type="button" @click="open = !open" class="flex items-center gap-1.5 flex-1 min-w-0 text-left">
                            <flux:icon name="chevron-right" class="size-4 text-zinc-400 shrink-0 transition-transform duration-150" :class="open ? 'rotate-90' : ''" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-200 font-medium truncate" x-text="item.alt || (item.image ? item.image.split('/').pop() : null) || item.title || item.name || item.label || ('Item ' + (idx + 1))"></span>
                        </button>
                        <button
                            type="button"
                            @click="removeItem(idx)"
                            class="text-zinc-400 hover:text-red-500 transition-colors shrink-0"
                            title="Remove item"
                        >
                            <flux:icon name="x-mark" class="size-3.5" />
                        </button>
                    </div>
                    {{-- Expanded fields --}}
                    <div x-show="open" x-transition class="border-t border-zinc-200 dark:border-zinc-700 px-3 pt-2 pb-3 space-y-2">
                    <template x-for="fKey in keys" :key="fKey">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400 mb-1" x-text="fKey"></p>
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
                                    @once('heroicon-modal-icons')
                                        @php
                                            $allIconsData = require resource_path('heroicons/data.php');
                                            $outlineIcons = array_keys($allIconsData['outline']);
                                            $solidIcons   = array_keys($allIconsData['solid']);
                                        @endphp
                                    @endonce
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
                            <template x-if="fKey === 'desc' || fKey === 'description'">
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
                            <template x-if="fKey !== 'icon' && fKey !== 'desc' && fKey !== 'description' && fKey !== 'image' && !fKey.endsWith('_image')">
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
            <button wire:click="resetContentField('{{ $field['key'] }}')" type="button" class="ml-auto text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
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

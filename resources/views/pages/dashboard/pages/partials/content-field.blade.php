<div wire:key="field-{{ $field['key'] }}" x-show="{{ $field['type'] === 'classes' ? 'designMode' : '!designMode' }}">
    @if ($field['type'] !== 'toggle')
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
        <flux:textarea
            wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
            rows="2"
            placeholder="{{ $field['default'] }}"
            class="font-mono text-xs"
        />
        <flux:text class="text-xs text-zinc-400 mt-1">Tailwind CSS classes.</flux:text>
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

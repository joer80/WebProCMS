<?php

use App\Jobs\RebuildAssets;
use App\Models\Setting;
use App\Support\ButtonStyleSyncer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Button Styles')] class extends Component {
    /** @var array<string, string> */
    public array $buttonClasses = [];

    public bool $saving = false;

    public function mount(): void
    {
        $this->loadButtonClasses();
    }

    public function save(): void
    {
        $this->saving = true;

        Setting::set('buttons.classes', $this->buttonClasses);

        app(ButtonStyleSyncer::class)->syncAll($this->buttonClasses);

        if (app()->isProduction() || config('cms.rebuild_assets_locally')) {
            defer(fn () => (new RebuildAssets)->handle());
        }

        $this->saving = false;
        $this->dispatch('notify', message: 'Button styles saved. Rebuilding CSS…');
    }

    protected function loadButtonClasses(): void
    {
        $defaults = $this->defaults();
        $saved = (array) Setting::get('buttons.classes', []);

        foreach ($defaults as $variant => $default) {
            $this->buttonClasses[$variant] = $saved[$variant] ?? $default;
        }
    }

    /** @return array<string, string> */
    protected function defaults(): array
    {
        return \App\Support\ButtonStyleSyncer::defaults();
    }
}; ?>

<div>
    <flux:main>
        @php
        $variants = [
            'primary' => [
                'label' => 'Primary',
                'description' => 'Main call-to-action. Used on heroes, CTAs, and pricing cards.',
                'preview_bg' => 'bg-zinc-100 dark:bg-zinc-800',
                'presets' => [
                    'Rounded' => 'inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors',
                    'Pill' => 'inline-flex items-center justify-center px-8 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary/90 transition-colors',
                    'Sharp' => 'inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-semibold hover:bg-primary/90 transition-colors',
                ],
            ],
            'secondary' => [
                'label' => 'Secondary',
                'description' => 'Paired with primary buttons as an alternative action.',
                'preview_bg' => 'bg-zinc-100 dark:bg-zinc-800',
                'presets' => [
                    'Outline' => 'inline-flex items-center justify-center px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors',
                    'Pill Outline' => 'inline-flex items-center justify-center px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-full hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors',
                    'Solid Zinc' => 'inline-flex items-center justify-center px-6 py-3 bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white font-semibold rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors',
                ],
            ],
            'ghost' => [
                'label' => 'Ghost',
                'description' => 'Text-only, no background or border. Used for minimal secondary actions.',
                'preview_bg' => 'bg-zinc-100 dark:bg-zinc-800',
                'presets' => [
                    'Text' => 'inline-flex items-center justify-center px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors',
                    'Underline' => 'inline-flex items-center justify-center px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold underline underline-offset-4 hover:text-zinc-900 dark:hover:text-white transition-colors',
                    'Primary Text' => 'inline-flex items-center justify-center px-6 py-3 text-primary font-semibold hover:text-primary/80 transition-colors',
                ],
            ],
            'inverted' => [
                'label' => 'Inverted',
                'description' => 'White fill with primary-coloured text. Used on dark or primary-coloured section backgrounds.',
                'preview_bg' => 'bg-primary',
                'presets' => [
                    'White/Primary' => 'inline-flex items-center justify-center px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors',
                    'White/Dark' => 'inline-flex items-center justify-center px-8 py-3 bg-white text-zinc-900 font-semibold rounded-lg hover:bg-zinc-100 transition-colors',
                    'White Pill' => 'inline-flex items-center justify-center px-8 py-3 bg-white text-primary font-semibold rounded-full hover:bg-zinc-100 transition-colors',
                ],
            ],
            'outline_white' => [
                'label' => 'Outline White',
                'description' => 'White border and text. Used on gradient or dark image backgrounds.',
                'preview_bg' => 'bg-primary',
                'presets' => [
                    'Outline White' => 'inline-flex items-center justify-center px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors',
                    'Solid White/30' => 'inline-flex items-center justify-center px-8 py-3 bg-white/10 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/20 transition-colors',
                    'Pill' => 'inline-flex items-center justify-center px-8 py-3 border border-white/30 text-white font-semibold rounded-full hover:bg-white/10 transition-colors',
                ],
            ],
            'danger' => [
                'label' => 'Danger',
                'description' => 'Destructive actions such as deleting records.',
                'preview_bg' => 'bg-zinc-100 dark:bg-zinc-800',
                'presets' => [
                    'Red Solid' => 'inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors',
                    'Red Outline' => 'inline-flex items-center justify-center px-6 py-3 border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors',
                    'Red Pill' => 'inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 transition-colors',
                ],
            ],
        ];
        @endphp

        <div class="max-w-5xl">
        <div class="mb-8 flex items-start justify-between gap-6">
            <div>
                <flux:heading size="xl">Button Styles</flux:heading>
                <flux:text class="mt-1">Customise the Tailwind classes for each button variant. Changes rebuild <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">public.css</code> automatically.</flux:text>
            </div>
            <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled" class="shrink-0">
                <span wire:loading.remove wire:target="save">Save &amp; Rebuild</span>
                <span wire:loading wire:target="save">Saving…</span>
            </flux:button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($variants as $key => $variant)
                <div
                    class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
                    data-classes="{{ $buttonClasses[$key] }}"
                    x-data="{
                        classes: '',
                        init() { this.classes = this.$el.dataset.classes; },
                        apply(preset) { this.classes = preset; this.$wire.set('buttonClasses.{{ $key }}', preset); },
                        onInput(val) { this.classes = val; this.$wire.set('buttonClasses.{{ $key }}', val); },
                    }"
                >
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-2">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $variant['label'] }}</div>
                            <code class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">btn-{{ str_replace('_', '-', $key) }}</code>
                        </div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $variant['description'] }}</div>
                    </div>

                    {{-- Preview --}}
                    <div class="px-6 py-6 {{ $variant['preview_bg'] }} flex items-center justify-center min-h-[5rem]">
                        <a href="#" x-bind:class="classes" @click.prevent>Button Text</a>
                    </div>

                    {{-- Presets --}}
                    <div class="px-6 py-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mr-1">Presets:</span>
                            @foreach ($variant['presets'] as $presetLabel => $presetClasses)
                                <button
                                    type="button"
                                    @click="apply(@js($presetClasses))"
                                    class="text-xs px-2.5 py-1 rounded border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary transition-colors"
                                >
                                    {{ $presetLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Classes field --}}
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">Tailwind classes</label>
                        <textarea
                            rows="4"
                            x-bind:value="classes"
                            @input="onInput($event.target.value)"
                            placeholder="inline-flex items-center justify-center px-6 py-3 ..."
                            class="w-full font-mono text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                            spellcheck="false"
                        ></textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5 bg-zinc-50 dark:bg-zinc-800/50">
                <flux:heading size="sm">Size &amp; width modifiers</flux:heading>
                <flux:text class="mt-1 text-sm">These are not editable here — compose them with any variant in your templates.</flux:text>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach (['btn-sm' => 'Smaller padding + text', 'btn-lg' => 'Larger padding + text', 'btn-full' => 'Full width'] as $cls => $desc)
                        <div class="flex items-center gap-2 text-sm">
                            <code class="text-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 rounded font-mono">{{ $cls }}</code>
                            <span class="text-zinc-500 dark:text-zinc-400">{{ $desc }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-start">
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save &amp; Rebuild</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </div>
        </div>
    </flux:main>
</div>

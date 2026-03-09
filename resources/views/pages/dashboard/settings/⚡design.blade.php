<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Design')] class extends Component {
    public string $bodyFont = 'instrument-sans';

    public string $headingFont = 'instrument-sans';

    public string $sectionSpacing = 'medium';

    public bool $altRowsEnabled = true;

    public function mount(): void
    {
        $this->altRowsEnabled = (bool) config('branding.alt_rows_enabled', true);
        $this->loadTypography();
    }

    public function saveAltRows(): void
    {
        $this->writeBrandingConfig();

        $this->dispatch('notify', message: 'Alt row setting saved.');
    }

    public function saveTypography(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $this->validate([
            'bodyFont' => ['required', 'string', 'in:instrument-sans,inter,system'],
            'headingFont' => ['required', 'string', 'in:instrument-sans,inter,system'],
            'sectionSpacing' => ['required', 'string', 'in:small,medium,large'],
        ]);

        $fontSansValue = $this->fontStack($this->bodyFont);
        $fontHeadingValue = $this->fontStack($this->headingFont);
        $spacingValues = $this->spacingValues($this->sectionSpacing);

        $appCssPath = resource_path('css/app.css');
        $appCss = file_get_contents($appCssPath);
        $appCss = preg_replace('/(--font-sans:\s*)([^;]+)(;)/', '${1}' . $fontSansValue . '${3}', $appCss);
        $appCss = preg_replace('/(--font-heading:\s*)([^;]+)(;)/', '${1}' . $fontHeadingValue . '${3}', $appCss);
        $appCss = preg_replace('/(--spacing-section:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['section'] . '${2}', $appCss);
        $appCss = preg_replace('/(--spacing-section-banner:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['banner'] . '${2}', $appCss);
        $appCss = preg_replace('/(--spacing-section-hero:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['hero'] . '${2}', $appCss);
        file_put_contents($appCssPath, $appCss);

        foreach ([resource_path('css/public.css'), resource_path('css/editor.css')] as $path) {
            $css = file_get_contents($path);
            $css = preg_replace('/(--font-sans:\s*)([^;]+)(;)/', '${1}' . $fontSansValue . '${3}', $css);
            $css = preg_replace('/(--spacing-section:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['section'] . '${2}', $css);
            $css = preg_replace('/(--spacing-section-banner:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['banner'] . '${2}', $css);
            $css = preg_replace('/(--spacing-section-hero:)\s*[^;]+(;[^\/]*)/', '${1} ' . $spacingValues['hero'] . '${2}', $css);
            file_put_contents($path, $css);
        }

        $this->writeBrandingConfig();

        $this->dispatch('notify', message: 'Typography saved. Rebuild assets to apply.');
    }

    protected function loadTypography(): void
    {
        $this->bodyFont = config('branding.body_font');
        $this->headingFont = config('branding.heading_font');
        $this->sectionSpacing = config('branding.section_spacing', 'medium');
    }

    protected function writeBrandingConfig(): void
    {
        $path = config_path('branding.php');
        $e = fn (string $v): string => str_replace("'", "\\'", $v);

        $altRowsPhp = $this->altRowsEnabled ? 'true' : 'false';

        file_put_contents($path, implode("\n", [
            '<?php',
            '',
            'return [',
            '',
            "    'logo_url' => '{$e(config('branding.logo_url', ''))}',",
            "    'body_font' => '{$e($this->bodyFont)}',",
            "    'heading_font' => '{$e($this->headingFont)}',",
            "    'section_spacing' => '{$e($this->sectionSpacing)}',",
            "    'alt_rows_enabled' => {$altRowsPhp},",
            '',
            '];',
            '',
        ]));

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        config([
            'branding.body_font' => $this->bodyFont,
            'branding.heading_font' => $this->headingFont,
            'branding.section_spacing' => $this->sectionSpacing,
            'branding.alt_rows_enabled' => $this->altRowsEnabled,
        ]);
    }

    /** @return array{section: string, banner: string, hero: string} */
    private function spacingValues(string $size): array
    {
        return match ($size) {
            'small' => ['section' => '4rem', 'banner' => '3rem', 'hero' => '5rem'],
            'large' => ['section' => '6rem', 'banner' => '5rem', 'hero' => '7rem'],
            default => ['section' => '5rem', 'banner' => '3rem', 'hero' => '6rem'],
        };
    }

    private function fontStack(string $slug): string
    {
        $fallbacks = "ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'";

        return match ($slug) {
            'instrument-sans' => "'Instrument Sans', {$fallbacks}",
            'inter' => "'Inter', {$fallbacks}",
            default => $fallbacks,
        };
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Design</flux:heading>
            <flux:text class="mt-1">Typography, spacing, and layout defaults for your site.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Alt Row Backgrounds</flux:heading>
                        <flux:text class="mt-1">Apply an alternating background color to every other section across all pages by default. Individual pages and rows can override this setting.</flux:text>
                        <div class="mt-4">
                            <flux:switch wire:model="altRowsEnabled" label="Enable alt row backgrounds site-wide" />
                        </div>
                    </div>
                    <flux:button wire:click="saveAltRows" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 {{ ! app()->isLocal() ? 'opacity-50' : '' }}">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Typography &amp; Spacing</flux:heading>
                        <flux:text class="mt-1">Font families and section spacing. Changes must be committed to git and assets rebuilt to take effect in production.</flux:text>
                        @if (! app()->isLocal())
                            <flux:text class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                ⚠ Typography editing is only available in your local environment. Make changes locally and commit to git.
                            </flux:text>
                        @endif
                        <div class="mt-4 space-y-4 {{ ! app()->isLocal() ? 'pointer-events-none select-none' : '' }}">
                            <flux:select wire:model="bodyFont" label="Body Font" description="Font used for all body text (--font-sans).">
                                <option value="instrument-sans">Instrument Sans</option>
                                <option value="inter">Inter</option>
                                <option value="system">System Font</option>
                            </flux:select>
                            <flux:select wire:model="headingFont" label="Heading Font" description="Font used with the font-heading class on headings (--font-heading).">
                                <option value="instrument-sans">Instrument Sans</option>
                                <option value="inter">Inter</option>
                                <option value="system">System Font</option>
                            </flux:select>
                            <div>
                                <flux:label>Section Spacing</flux:label>
                                <flux:text class="mt-1 text-xs text-zinc-500">Vertical padding for page sections. Controls py-section (standard), py-section-banner (compact CTAs), and py-section-hero (heroes).</flux:text>
                                <div class="mt-2 flex gap-2">
                                    @foreach (['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('sectionSpacing', '{{ $value }}')"
                                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors {{ $sectionSpacing === $value ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600 hover:border-zinc-400' }}"
                                        >{{ $label }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveTypography" variant="outline" class="shrink-0" :disabled="! app()->isLocal()">Save</flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>

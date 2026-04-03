<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class BrandingStyleService
{
    public function styleBlock(): string
    {
        return Cache::rememberForever('branding_style_block', fn () => $this->build());
    }

    public function bust(): void
    {
        Cache::forget('branding_style_block');
    }

    /** @return array<string, string> */
    public function defaultColors(): array
    {
        return [
            'primary' => '#3B4A99',
            'primary-hover' => '#2f3b7a',
            'primary-foreground' => '#ffffff',
            'primary-surface' => '#eef0f9',
            'accent' => '#262626',
            'accent-content' => '#262626',
            'accent-foreground' => '#ffffff',
        ];
    }

    /** @return array{section: string, banner: string, hero: string} */
    public function spacingValues(string $size): array
    {
        return match ($size) {
            'small' => ['section' => '4rem', 'banner' => '3rem', 'hero' => '5rem'],
            'large' => ['section' => '6rem', 'banner' => '5rem', 'hero' => '7rem'],
            default => ['section' => '5rem', 'banner' => '3rem', 'hero' => '6rem'],
        };
    }

    public function containerWidthValue(string $size): string
    {
        return match ($size) {
            'small' => '56rem',
            'large' => '80rem',
            default => '72rem',
        };
    }

    public function fontStack(string $slug): string
    {
        $fallbacks = "ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'";

        return match ($slug) {
            'instrument-sans' => "'Instrument Sans', {$fallbacks}",
            'inter' => "'Inter', {$fallbacks}",
            default => $fallbacks,
        };
    }

    private function build(): string
    {
        $colors = (array) Setting::get('branding.colors', $this->defaultColors());
        $bodyFont = (string) Setting::get('branding.body_font', 'instrument-sans');
        $headingFont = (string) Setting::get('branding.heading_font', 'instrument-sans');
        $spacing = $this->spacingValues((string) Setting::get('branding.section_spacing', 'medium'));
        $containerWidth = $this->containerWidthValue((string) Setting::get('branding.container_width', 'medium'));

        $vars = [];

        foreach ($colors as $name => $value) {
            if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
                $vars[] = "    --color-{$name}: {$value};";
            }
        }

        $vars[] = "    --font-sans: {$this->fontStack($bodyFont)};";
        $vars[] = "    --font-heading: {$this->fontStack($headingFont)};";
        $vars[] = "    --spacing-section: {$spacing['section']};";
        $vars[] = "    --spacing-section-banner: {$spacing['banner']};";
        $vars[] = "    --spacing-section-hero: {$spacing['hero']};";
        $vars[] = "    --width-container: {$containerWidth};";

        return ":root {\n".implode("\n", $vars)."\n}";
    }
}

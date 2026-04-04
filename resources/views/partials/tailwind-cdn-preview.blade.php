@php
    $publicCss = file_get_contents(resource_path('css/public.css'));

    // Strip directives the browser CDN cannot handle:
    // - @import '...'          — CDN provides tailwindcss itself; relative imports are not supported
    // - @plugin "..."          — plugins are not supported in the browser CDN
    // - @source '../...'       — file-path sources (CDN scans the live DOM instead)
    // @source inline("...")    — class-list sources are kept; CDN honours them
    // @theme, @layer, @utility, @custom-variant — all kept
    $lines = array_filter(
        explode("\n", $publicCss),
        function (string $line): bool {
            $t = ltrim($line);
            if (str_starts_with($t, '@import')) {
                return false;
            }
            if (str_starts_with($t, '@plugin')) {
                return false;
            }
            if (preg_match("/@source\s+['\"]\.\.\/[^'\"]+['\"];/", $t)) {
                return false;
            }

            return true;
        },
    );

    $themeContent = implode("\n", $lines);

    // Inline imported CSS files that the CDN cannot @import (e.g. buttons.css)
    $buttonsCssPath = resource_path('css/buttons.css');
    if (file_exists($buttonsCssPath)) {
        $themeContent .= "\n" . file_get_contents($buttonsCssPath);
    }
@endphp
<style type="text/tailwindcss">
{!! $themeContent !!}
{!! app(\App\Services\BrandingStyleService::class)->styleBlock() !!}
</style>
<script src="https://unpkg.com/@tailwindcss/browser@4"></script>

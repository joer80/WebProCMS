<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

@if (! empty($description))
    <meta name="description" content="{{ $description }}" />
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@stack('head')
<link rel="stylesheet" href="https://unpkg.com/trix@2.1.1/dist/trix.css">
<style>
    /* Trix editor container */
    trix-toolbar { border: 1px solid rgb(212 212 216); border-bottom: none; border-radius: 0.375rem 0.375rem 0 0; padding: 0.25rem 0.5rem; background: rgb(244 244 245); }
    trix-editor { display: block; border: 1px solid rgb(212 212 216); border-radius: 0 0 0.375rem 0.375rem; min-height: 16rem; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.625; color: rgb(24 24 27); background: #fff; outline: none; }
    trix-editor:focus { border-color: rgb(161 161 170); }
    /* Dark mode container */
    .dark trix-toolbar { border-color: rgb(63 63 70); background: rgb(39 39 42); }
    .dark trix-editor { border-color: rgb(63 63 70); color: rgb(250 250 250); background: rgb(24 24 27); }
    .dark trix-editor:focus { border-color: rgb(113 113 122); }
    /* Dark mode buttons: invert the dark SVG icons so they're visible on a dark toolbar */
    .dark trix-toolbar .trix-button-group { border-color: rgb(63 63 70); }
    .dark trix-toolbar .trix-button { color: rgb(209 213 219); border-color: rgb(63 63 70); }
    .dark trix-toolbar .trix-button::before { filter: invert(0.85); }
    .dark trix-toolbar .trix-button:not(:disabled):hover { background: rgb(55 65 81); }
    .dark trix-toolbar .trix-button:not(:disabled):hover::before { filter: invert(1); }
    /* Active state: blue so it's immediately obvious the format is on */
    .dark trix-toolbar .trix-button.trix-active { background: rgb(37 99 235); color: #fff; }
    .dark trix-toolbar .trix-button.trix-active::before { filter: invert(1); }
    /* Trix rendered content (editor + public page) */
    .trix-content { font-size: 1rem; line-height: 1.75; }
    .trix-content h1 { font-size: 1.875rem; font-weight: 600; margin-top: 1.5em; margin-bottom: 0.5em; }
    .trix-content h2 { font-size: 1.5rem; font-weight: 600; margin-top: 1.5em; margin-bottom: 0.5em; }
    .trix-content h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.25em; margin-bottom: 0.5em; }
    .trix-content p { margin-bottom: 1em; }
    /* Lists: Trix CDN resets all padding/margin via .trix-content * { padding:0; margin:0 }
       and indents li with margin-left. Tailwind preflight sets list-style:none on all ul/ol.
       We override both here with higher specificity. */
    .trix-content ul { list-style-type: disc; padding-left: 1.75em; margin-bottom: 1em; margin-left: 0; }
    .trix-content ol { list-style-type: decimal; padding-left: 1.75em; margin-bottom: 1em; margin-left: 0; }
    .trix-content li { display: list-item; margin: 0 0 0.25em 0; padding: 0; } /* zero out Trix's margin-left:1em on li */
    .trix-content li::marker { color: currentColor; }
    .trix-content blockquote { border-left: 3px solid #d4d4d8; padding-left: 1em; margin: 1em 0; color: #71717a; }
    .trix-content pre { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 1em; border-radius: 0.375rem; overflow-x: auto; margin-bottom: 1em; font-size: 0.875em; }
    .trix-content code { font-family: ui-monospace, monospace; font-size: 0.875em; background: #e2e8f0; border: 1px solid #cbd5e1; padding: 0.1em 0.4em; border-radius: 0.25em; }
    .trix-content a { color: #2563eb; text-decoration: underline; }
    .dark .trix-content blockquote { border-color: #3f3f46; color: #a1a1aa; }
    .dark .trix-content pre { background: rgb(9 9 11); border: 1px solid rgb(63 63 70); color: rgb(212 212 216); }
    .dark .trix-content code { background: rgb(51 65 85); border: 1px solid rgb(100 116 139); color: rgb(203 213 225); }
    .dark .trix-content li::marker { color: rgb(212 212 216); }
    .dark .trix-content a { color: #60a5fa; }
</style>

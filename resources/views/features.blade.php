<x-layouts::public title="Features — WebProCMS" description="Explore the powerful features that make WebProCMS the CMS of choice for developers, agencies, and content teams.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Everything you need</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Built for how<br>pros actually work.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            WebProCMS packs every tool a developer or agency needs into one clean, fast platform — with nothing you don't.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    Start for free
                </a>
            @endif
            <a href="{{ route('pricing') }}" class="inline-block px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                See pricing
            </a>
        </div>
    </section>

    {{-- Content editing --}}
    <section class="mb-24">
        <div class="mb-10">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Content editing</span>
            <h2 class="text-3xl font-semibold leading-tight">Write, edit, and publish with confidence.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                ['Visual Block Editor', 'document-text', 'Build content with rich text, images, video embeds, code blocks, and custom block types — all in a clean, distraction-free interface.'],
                ['Version History', 'clock', 'Every save is tracked. Roll back to any previous version with a single click and never lose work again.'],
                ['Content Scheduling', 'calendar', 'Write now, publish later. Schedule posts and pages to go live at exactly the right moment.'],
                ['Multi-tab Editing', 'squares-2x2', 'Work on multiple content pieces side by side without losing your place or your context.'],
                ['Live Preview', 'eye', 'See exactly how content looks on desktop, tablet, and mobile before publishing.'],
                ['Keyboard-first Workflow', 'command-line', 'Full keyboard navigation and shortcuts throughout the editor so your hands never leave the keys.'],
            ] as [$title, $icon, $desc])
                <flux:card class="flex flex-col gap-3">
                    <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                    <flux:heading size="lg">{{ $title }}</flux:heading>
                    <flux:text>{{ $desc }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- Developer tools --}}
    <section class="mb-24">
        <div class="mb-10">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Developer tools</span>
            <h2 class="text-3xl font-semibold leading-tight">Extend and integrate without fighting the framework.</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                ['REST API', 'Serve content to any frontend, mobile app, or third-party tool via a full REST API included out of the box.'],
                ['Custom Block Types', 'Define your own content structures and render them exactly the way your design requires.'],
                ['Shortcodes', 'Drop reusable content snippets anywhere — dynamic phone numbers, addresses, CTAs, and more.'],
                ['Webhook Support', 'Trigger external workflows when content is published, updated, or deleted.'],
            ] as [$title, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $title }}</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Media & SEO --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Media & SEO</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Every page optimised and every asset organised.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                Control meta titles, descriptions, canonical URLs, and Open Graph tags per page. The built-in media library keeps images and files organised and searchable.
            </p>
            <ul class="space-y-2">
                @foreach (['Per-page SEO fields', 'Open Graph & Twitter Cards', 'Schema.org structured data', 'Media library with search', 'Image alt text management', 'noindex/nofollow controls'] as $item)
                    <li class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        <flux:icon name="check-circle" class="size-4 text-primary dark:text-primary-surface shrink-0" />
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 space-y-4">
            @foreach ([
                ['Meta title', 'Getting Started with WebProCMS | WebProCMS'],
                ['Meta description', 'Learn how to set up your first site, add content, and publish in under 10 minutes.'],
                ['OG image', '/images/og-getting-started.jpg'],
            ] as [$label, $value])
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] mb-1">{{ $label }}</p>
                    <p class="text-sm font-mono text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to explore every feature?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Sign up free and get full access to every feature. No credit card required.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Create a free account
                </a>
            @endif
            <a href="{{ route('pricing') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                View pricing
            </a>
        </div>
    </section>
</x-layouts::public>

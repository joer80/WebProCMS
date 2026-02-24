<x-layouts::public title="WebProCMS — Build, Manage, and Publish Without Limits" description="WebProCMS is a clean, powerful content management platform built for web professionals, developers, and agencies who demand more from their tools.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Now in public beta</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Build and manage<br>your web presence.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            WebProCMS is a clean, powerful content management platform built for web professionals, developers, and agencies who demand more from their tools.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    Start building free
                </a>
            @endif
            <a href="{{ route('about') }}" class="inline-block px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                Learn more
            </a>
        </div>
    </section>

    {{-- Hero visual: content editor mockup --}}
    <div class="mb-24 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 lg:p-10">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <div class="flex items-center gap-2 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                <span>Posts</span>
                <span>/</span>
                <span class="text-primary dark:text-primary-surface font-medium">Edit post</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2.5 py-1 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">Draft</span>
                <span class="text-xs px-2.5 py-1 bg-primary text-primary-foreground rounded">Publish</span>
            </div>
        </div>
        <p class="text-xl font-semibold text-primary dark:text-primary-surface mb-4">Getting Started with WebProCMS</p>
        <div class="flex items-center gap-1.5 mb-4 pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            @foreach (['B', 'I', 'H₁', 'Link', 'Image', 'Block'] as $tool)
                <span class="text-xs px-1.5 py-0.5 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] font-mono">{{ $tool }}</span>
            @endforeach
        </div>
        <div class="space-y-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
            <p>Web professionals deserve a CMS that gets out of the way. WebProCMS is built for developers, agencies, and content teams who need speed and flexibility without compromise.</p>
            <div class="h-16 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded border border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-center">
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">+ Add image block</span>
            </div>
            <p>Extend the editor with custom block types that fit your content model — or use the dozens of built-in blocks to get started right away.</p>
        </div>
    </div>

    {{-- Intro --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">What is WebProCMS?</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Your content, your way.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                Most CMS platforms were built for bloggers. WebProCMS is built for web professionals — developers, agencies, and content teams who need flexibility, speed, and control without compromise.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                Install it in minutes, customise it without limits, and publish across multiple sites from one clean dashboard.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ([
                ['Developer-first', 'Clean APIs, extensible architecture, and no magic you can\'t understand or control.'],
                ['Multi-site ready', 'Manage multiple websites and brands from a single, unified dashboard.'],
                ['Headless-capable', 'Use it as a traditional or headless CMS with our full REST API included.'],
                ['Team-friendly', 'Invite collaborators, set roles, and keep everyone working in sync.'],
            ] as [$title, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-5">
                    <p class="font-semibold text-sm mb-1">{{ $title }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-xs leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Services --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">What we offer</span>
            <h2 class="text-3xl font-semibold leading-tight">Everything you need, nothing you don't.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                [
                    'title' => 'Visual Content Editor',
                    'description' => 'Write and edit with a powerful block-based editor. Rich text, images, embeds, and custom blocks — all in one clean interface.',
                ],
                [
                    'title' => 'Page Builder',
                    'description' => 'Build beautiful pages without writing code. Drag-and-drop blocks, adjust layouts, and preview changes in real time.',
                ],
                [
                    'title' => 'Media Library',
                    'description' => 'Upload, organise, and optimise your images and files. Find anything in seconds with full-text search and folder organisation.',
                ],
                [
                    'title' => 'SEO & Meta Management',
                    'description' => 'Control meta titles, descriptions, canonical URLs, and Open Graph tags — per page, every time.',
                ],
                [
                    'title' => 'Content Scheduling',
                    'description' => 'Write now, publish later. Schedule content to go live at exactly the right moment from one content calendar.',
                ],
                [
                    'title' => 'Team & Role Management',
                    'description' => 'Invite collaborators, assign roles, and keep your team working efficiently with fine-grained permissions.',
                ],
            ] as $service)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $service['title'] }}</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $service['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- How it works --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">How it works</span>
            <h2 class="text-3xl font-semibold leading-tight">From setup to published in minutes.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                ['Create', 'document-text', 'Write, edit, and structure your content with a powerful block-based editor that adapts to any content type.'],
                ['Organise', 'folder-open', 'Manage posts, pages, categories, and custom content types from one clean, unified dashboard.'],
                ['Publish', 'rocket-launch', 'Send content live instantly, schedule it for later, or serve it through our API to any frontend.'],
            ] as [$title, $icon, $desc])
                <flux:card class="flex flex-col gap-3">
                    <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                    <flux:heading size="lg">{{ $title }}</flux:heading>
                    <flux:text>{{ $desc }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- Latest blog posts --}}
    @if ($recentPosts->isNotEmpty())
        <section class="mb-24">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">From the blog</span>
                    <h2 class="text-3xl font-semibold leading-tight">Latest posts.</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] hover:text-primary dark:hover:text-primary-surface transition-colors shrink-0 ml-6">
                    View all →
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recentPosts as $post)
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden">
                        @if ($post->featured_image)
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <img
                                    src="{{ $post->featuredImageUrl() }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-44 object-cover"
                                />
                            </a>
                        @endif

                        <div class="p-6 flex flex-col {{ $post->featured_image ? '' : 'h-full' }}">
                            @if ($post->category)
                                <a
                                    href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                                    class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] mb-3 hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors w-fit"
                                >
                                    {{ $post->category->name }}
                                </a>
                            @endif

                            <h3 class="font-semibold text-base leading-snug mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            @if ($post->excerpt)
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-relaxed mb-4 line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            @endif

                            <div class="mt-auto pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $post->published_at?->format('M j, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to build something great?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Join web professionals already using WebProCMS to power their sites and deliver great content.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Create a free account
                </a>
            @endif
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                Talk to us
            </a>
        </div>
    </section>
</x-layouts::public>

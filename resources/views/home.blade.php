@php
    $heroData = match($websiteType) {
        'service' => [
            'eyebrow'     => 'Professional services',
            'heading'     => "Quality service,<br>right in your area.",
            'subheading'  => 'We deliver expert services to homes and businesses across the region. Fast, reliable, and backed by years of experience.',
            'primaryCta'  => ['label' => 'Get a free quote', 'route' => 'contact'],
            'secondaryCta'=> ['label' => 'Our services', 'route' => 'services'],
        ],
        'ecommerce' => [
            'eyebrow'     => 'Free shipping on orders over $75',
            'heading'     => "Shop smarter.<br>Deliver faster.",
            'subheading'  => 'Discover a curated selection of products built for quality and longevity. Everything you need, nothing you don\'t.',
            'primaryCta'  => ['label' => 'Shop now', 'route' => 'products'],
            'secondaryCta'=> ['label' => 'Learn about us', 'route' => 'about'],
        ],
        'law' => [
            'eyebrow'     => 'Free initial consultation',
            'heading'     => "Strong advocacy.<br>Real results.",
            'subheading'  => 'Our experienced attorneys fight for your rights across a broad range of practice areas. Let\'s talk about your case today.',
            'primaryCta'  => ['label' => 'Book a consultation', 'route' => 'contact'],
            'secondaryCta'=> ['label' => 'Practice areas', 'route' => 'practice-areas'],
        ],
        'nonprofit' => [
            'eyebrow'     => 'Making a difference since 2006',
            'heading'     => "Every dollar.<br>Every hour. Counts.",
            'subheading'  => 'We\'re a community-driven nonprofit dedicated to changing lives through education, food security, and opportunity.',
            'primaryCta'  => ['label' => 'Donate today', 'route' => 'donate'],
            'secondaryCta'=> ['label' => 'Volunteer', 'route' => 'volunteer'],
        ],
        'healthcare' => [
            'eyebrow'     => 'Accepting new patients',
            'heading'     => "Your health is<br>our mission.",
            'subheading'  => 'Compassionate, comprehensive healthcare for patients and employers — with convenient locations and same-day appointments available.',
            'primaryCta'  => ['label' => 'Book an appointment', 'route' => 'contact'],
            'secondaryCta'=> ['label' => 'Find a location', 'route' => 'locations'],
        ],
        'custom' => [
            'eyebrow'     => 'Welcome',
            'heading'     => "Your vision.<br>Our platform.",
            'subheading'  => 'A clean, flexible platform built to adapt to your needs. Start exploring what\'s possible.',
            'primaryCta'  => ['label' => 'Get in touch', 'route' => 'contact'],
            'secondaryCta'=> ['label' => 'Learn more', 'route' => 'about'],
        ],
        default => [ // saas
            'eyebrow'     => 'Now in public beta',
            'heading'     => "Build and manage<br>your web presence.",
            'subheading'  => 'WebProCMS is a clean, powerful content management platform built for web professionals, developers, and agencies who demand more from their tools.',
            'primaryCta'  => ['label' => 'Start building free', 'route' => 'register'],
            'secondaryCta'=> ['label' => 'Learn more', 'route' => 'about'],
        ],
    };

    $introData = match($websiteType) {
        'service' => [
            'eyebrow' => 'Why choose us',
            'heading' => 'Reliable. Professional. Local.',
            'body'    => [
                'We\'ve been serving this community for over a decade, and our reputation is built on doing the job right the first time. Licensed, insured, and ready to help.',
                'From routine maintenance to complex projects, our team handles it all — on time and on budget.',
            ],
            'cards'   => [
                ['Local experts', 'We know your area and the challenges unique to your region.'],
                ['Licensed & insured', 'Full coverage for your peace of mind on every job.'],
                ['Transparent pricing', 'No surprises. You\'ll always know what to expect before we start.'],
                ['Satisfaction guarantee', 'We stand behind our work with a 100% satisfaction guarantee.'],
            ],
        ],
        'ecommerce' => [
            'eyebrow' => 'Why shop with us',
            'heading' => 'Quality you can count on.',
            'body'    => [
                'Every product in our catalog is hand-selected for quality, durability, and value. We don\'t stock anything we wouldn\'t use ourselves.',
                'With free returns, fast shipping, and a team that actually answers the phone — shopping here is easy.',
            ],
            'cards'   => [
                ['Curated selection', 'Only products that meet our quality standards make it into the catalog.'],
                ['Fast fulfillment', 'Orders ship within 1 business day from our warehouse.'],
                ['Easy returns', '30-day hassle-free returns. No questions asked.'],
                ['Real support', 'Live chat and phone support during business hours.'],
            ],
        ],
        'law' => [
            'eyebrow' => 'Our approach',
            'heading' => 'We fight for you.',
            'body'    => [
                'The legal system can be overwhelming. Our job is to guide you through it clearly and confidently — always putting your interests first.',
                'We take cases we believe in and give each client the attention they deserve. You\'re not a case number here.',
            ],
            'cards'   => [
                ['Free consultation', 'We\'ll review your case at no cost before you commit to anything.'],
                ['Experienced team', 'Decades of combined courtroom and negotiation experience.'],
                ['Clear communication', 'We explain everything in plain language and answer your calls.'],
                ['No win, no fee', 'Most personal injury cases handled on a contingency basis.'],
            ],
        ],
        'nonprofit' => [
            'eyebrow' => 'Our mission',
            'heading' => 'Building a stronger community.',
            'body'    => [
                'We believe every person deserves access to education, nutritious food, and a safe place to belong. Since 2006, we\'ve worked alongside this community to make that a reality.',
                'With your support — whether through donations, volunteering, or spreading the word — we can reach more people than ever before.',
            ],
            'cards'   => [
                ['Education programs', 'After-school tutoring and literacy support for 500+ students annually.'],
                ['Food security', 'A food pantry that serves over 1,000 families every month.'],
                ['Community events', 'Monthly gatherings that bring neighbours together.'],
                ['Transparent finances', '93% of every dollar goes directly to programs.'],
            ],
        ],
        'healthcare' => [
            'eyebrow' => 'How we care',
            'heading' => 'Comprehensive care close to home.',
            'body'    => [
                'From preventive wellness to occupational health, we offer a full range of services under one roof — so you get the care you need without the runaround.',
                'Our clinicians take the time to listen, explain, and work with you to create a care plan that fits your life.',
            ],
            'cards'   => [
                ['Patient-centred', 'Your care team knows you by name, not just your chart.'],
                ['HIPAA-compliant', 'Your health information is always private and secure.'],
                ['Multi-location', 'Multiple convenient clinics across the region.'],
                ['Same-day appointments', 'We offer same-day visits for urgent and primary care needs.'],
            ],
        ],
        'custom' => [
            'eyebrow' => 'About us',
            'heading' => 'Here to help.',
            'body'    => [
                'We\'re a team dedicated to delivering exceptional results for every client we work with. Whatever your need, we have the experience and commitment to help.',
                'Get in touch and let\'s talk about how we can work together.',
            ],
            'cards'   => [
                ['Quality work', 'We hold ourselves to the highest standards in everything we do.'],
                ['Trusted team', 'Years of experience and a track record of satisfied clients.'],
                ['Clear communication', 'You\'ll always know what\'s happening and what to expect.'],
                ['Flexible approach', 'We adapt to your needs, not the other way around.'],
            ],
        ],
        default => [ // saas
            'eyebrow' => 'What is WebProCMS?',
            'heading' => 'Your content, your way.',
            'body'    => [
                'Most CMS platforms were built for bloggers. WebProCMS is built for web professionals — developers, agencies, and content teams who need flexibility, speed, and control without compromise.',
                'Install it in minutes, customise it without limits, and publish across multiple sites from one clean dashboard.',
            ],
            'cards'   => [
                ['Developer-first', 'Clean APIs, extensible architecture, and no magic you can\'t understand or control.'],
                ['Multi-site ready', 'Manage multiple websites and brands from a single, unified dashboard.'],
                ['Headless-capable', 'Use it as a traditional or headless CMS with our full REST API included.'],
                ['Team-friendly', 'Invite collaborators, set roles, and keep everyone working in sync.'],
            ],
        ],
    };
@endphp

<x-layouts::public
    :title="$websiteType === 'saas' ? 'WebProCMS — Build, Manage, and Publish Without Limits' : config('app.name')"
    :description="$heroData['subheading']"
>
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ $heroData['eyebrow'] }}</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            {!! $heroData['heading'] !!}
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            {{ $heroData['subheading'] }}
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if ($heroData['primaryCta']['route'] === 'register' && Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    {{ $heroData['primaryCta']['label'] }}
                </a>
            @else
                <a href="{{ route($heroData['primaryCta']['route']) }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    {{ $heroData['primaryCta']['label'] }}
                </a>
            @endif
            <a href="{{ route($heroData['secondaryCta']['route']) }}" class="inline-block px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                {{ $heroData['secondaryCta']['label'] }}
            </a>
        </div>
    </section>

    @if ($websiteType === 'saas')
        {{-- SaaS hero visual: content editor mockup --}}
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
                @foreach (['B', 'I', 'H₁', 'Link', 'Image', 'Block'] as $editorTool)
                    <span class="text-xs px-1.5 py-0.5 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] font-mono">{{ $editorTool }}</span>
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
    @endif

    {{-- Intro / value props --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">{{ $introData['eyebrow'] }}</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">{{ $introData['heading'] }}</h2>
            @foreach ($introData['body'] as $paragraph)
                <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">{{ $paragraph }}</p>
            @endforeach
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ($introData['cards'] as [$cardTitle, $cardDesc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-5">
                    <p class="font-semibold text-sm mb-1">{{ $cardTitle }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-xs leading-normal">{{ $cardDesc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if ($websiteType === 'saas')
        {{-- SaaS: Services section --}}
        <section class="mb-24">
            <div class="text-center mb-12">
                <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">What we offer</span>
                <h2 class="text-3xl font-semibold leading-tight">Everything you need, nothing you don't.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['Visual Content Editor', 'Write and edit with a powerful block-based editor. Rich text, images, embeds, and custom blocks — all in one clean interface.'],
                    ['Page Builder', 'Build beautiful pages without writing code. Drag-and-drop blocks, adjust layouts, and preview changes in real time.'],
                    ['Media Library', 'Upload, organise, and optimise your images and files. Find anything in seconds with full-text search and folder organisation.'],
                    ['SEO & Meta Management', 'Control meta titles, descriptions, canonical URLs, and Open Graph tags — per page, every time.'],
                    ['Content Scheduling', 'Write now, publish later. Schedule content to go live at exactly the right moment from one content calendar.'],
                    ['Team & Role Management', 'Invite collaborators, assign roles, and keep your team working efficiently with fine-grained permissions.'],
                ] as [$serviceTitle, $serviceDesc])
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                        <h3 class="font-semibold mb-2">{{ $serviceTitle }}</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $serviceDesc }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- SaaS: How it works --}}
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
                ] as [$stepTitle, $stepIcon, $stepDesc])
                    <flux:card class="flex flex-col gap-3">
                        <flux:icon :name="$stepIcon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                        <flux:heading size="lg">{{ $stepTitle }}</flux:heading>
                        <flux:text>{{ $stepDesc }}</flux:text>
                    </flux:card>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Latest blog posts (shown for types that have Blog in nav) --}}
    @if ($recentPosts->isNotEmpty() && in_array($websiteType, ['saas', 'service', 'nonprofit', 'custom']))
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
        @if ($websiteType === 'saas')
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
        @elseif ($websiteType === 'nonprofit')
            <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Join us in making a difference.</h2>
            <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
                Whether you give, volunteer, or simply share our mission — every action counts.
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="{{ route('donate') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Donate now
                </a>
                <a href="{{ route('volunteer') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                    Volunteer
                </a>
            </div>
        @else
            <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to get started?</h2>
            <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
                We're here to help. Reach out today and let's talk about what we can do for you.
            </p>
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                Get in touch
            </a>
        @endif
    </section>
</x-layouts::public>

<x-layouts::public title="Services — GetRows">
    {{-- Hero --}}
    <section class="mb-16 text-center">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">What we offer</span>
        <h1 class="text-4xl font-semibold leading-tight mb-4">Everything you need to work with your data.</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl mx-auto">
            From instant SQL queries to team-wide collaboration tools, GetRows gives you the building blocks to make sense of your data — fast.
        </p>
    </section>

    {{-- Core services --}}
    <section class="mb-24">
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                [
                    'icon' => 'code-bracket',
                    'title' => 'Instant Query Editor',
                    'description' => 'Write SQL with intelligent autocomplete, syntax highlighting, and real-time error hints. Results appear as you type — no page reload, no waiting.',
                    'features' => ['Syntax highlighting', 'Autocomplete with schema awareness', 'Real-time error detection', 'Multi-tab support'],
                ],
                [
                    'icon' => 'cursor-arrow-rays',
                    'title' => 'Visual Query Builder',
                    'description' => 'Not a SQL expert? Build queries by clicking — filter, sort, group, and join tables without writing a single line of code.',
                    'features' => ['Drag-and-drop interface', 'Join tables visually', 'Filter & sort builder', 'Auto-generates SQL'],
                ],
                [
                    'icon' => 'share',
                    'title' => 'Live Shared Results',
                    'description' => 'Share a query result as a live link. Recipients always see up-to-date data without needing database access or a GetRows account.',
                    'features' => ['Public & private links', 'Auto-refreshing data', 'Password protection', 'Expiry controls'],
                ],
                [
                    'icon' => 'arrow-down-tray',
                    'title' => 'Export Anywhere',
                    'description' => 'Download results as CSV, JSON, or Excel. Push directly to Google Sheets, Notion, or your favourite BI tool in one click.',
                    'features' => ['CSV, JSON & Excel', 'Google Sheets integration', 'Notion export', 'Scheduled exports'],
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Query History',
                    'description' => 'Every query is saved automatically. Search, replay, and fork previous queries in one click. Never lose work again.',
                    'features' => ['Full query log', 'Full-text search', 'Fork & remix', 'Team-shared history'],
                ],
                [
                    'icon' => 'shield-check',
                    'title' => 'Role-based Access',
                    'description' => 'Keep sensitive tables safe. Grant read-only access to specific users or teams without touching database permissions.',
                    'features' => ['Custom roles & permissions', 'Table-level restrictions', 'Audit logs', 'SSO support'],
                ],
            ] as $service)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-md bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center shrink-0">
                            <flux:icon :name="$service['icon']" class="text-[#706f6c] dark:text-[#A1A09A]" />
                        </div>
                        <h3 class="font-semibold">{{ $service['title'] }}</h3>
                    </div>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal mb-4">{{ $service['description'] }}</p>
                    <ul class="space-y-1.5">
                        @foreach ($service['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm text-primary dark:text-primary-surface">
                                <flux:icon name="check-circle" variant="micro" class="text-green-500 dark:text-green-400 shrink-0" />
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Pricing --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Pricing</span>
            <h2 class="text-3xl font-semibold leading-tight">Simple, transparent pricing.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] mt-3 leading-normal max-w-lg mx-auto">
                Start for free and upgrade as your team grows. No hidden fees, no surprise charges.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 items-start">
            @foreach ([
                [
                    'name' => 'Starter',
                    'price' => 'Free',
                    'period' => 'forever',
                    'description' => 'For individuals exploring their data.',
                    'features' => ['1 database connection', '100 queries / month', 'CSV & JSON export', 'Community support'],
                    'cta' => 'Get started',
                    'variant' => 'default',
                ],
                [
                    'name' => 'Pro',
                    'price' => '$29',
                    'period' => '/ month',
                    'description' => 'For teams that need more power.',
                    'features' => ['Up to 5 database connections', 'Unlimited queries', 'All export formats', 'Live shared results', 'Query history', 'Priority support'],
                    'cta' => 'Start free trial',
                    'variant' => 'primary',
                ],
                [
                    'name' => 'Enterprise',
                    'price' => 'Custom',
                    'period' => '',
                    'description' => 'For large teams with advanced needs.',
                    'features' => ['Unlimited connections', 'Role-based access control', 'SSO & audit logs', 'Scheduled exports', 'Dedicated support', 'SLA guarantee'],
                    'cta' => 'Talk to us',
                    'variant' => 'default',
                ],
            ] as $plan)
                @php $isPrimary = $plan['variant'] === 'primary'; @endphp
                <div @class([
                    'rounded-lg p-6',
                    'bg-primary dark:bg-primary-foreground shadow-[inset_0px_0px_0px_1px_rgba(255,255,255,0.1)] dark:shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]' => $isPrimary,
                    'bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]' => ! $isPrimary,
                ])>
                    <p @class([
                        'text-xs font-semibold tracking-widest uppercase mb-1',
                        'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                        'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                    ])>{{ $plan['name'] }}</p>
                    <div class="flex items-end gap-1 mb-1">
                        <span @class([
                            'text-3xl font-semibold',
                            'text-primary-foreground dark:text-primary' => $isPrimary,
                            'text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['price'] }}</span>
                        @if ($plan['period'])
                            <span @class([
                                'text-sm mb-1',
                                'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                                'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                            ])>{{ $plan['period'] }}</span>
                        @endif
                    </div>
                    <p @class([
                        'text-sm leading-normal mb-5',
                        'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                        'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                    ])>{{ $plan['description'] }}</p>
                    <ul class="space-y-2 mb-6">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm">
                                <flux:icon name="check-circle" variant="micro" @class([
                                    'shrink-0',
                                    'text-green-400 dark:text-green-500' => $isPrimary,
                                    'text-green-500 dark:text-green-400' => ! $isPrimary,
                                ]) />
                                <span @class([
                                    'text-primary-foreground dark:text-primary' => $isPrimary,
                                    'text-primary dark:text-primary-surface' => ! $isPrimary,
                                ])>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($plan['name'] === 'Enterprise')
                        <a href="{{ route('contact') }}" @class([
                            'block text-center px-4 py-2.5 rounded-sm text-sm font-medium leading-normal transition-all',
                            'bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground hover:bg-neutral-100 dark:hover:bg-primary-hover' => $isPrimary,
                            'border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['cta'] }}</a>
                    @elseif (Route::has('register'))
                        <a href="{{ route('register') }}" @class([
                            'block text-center px-4 py-2.5 rounded-sm text-sm font-medium leading-normal transition-all',
                            'bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground hover:bg-neutral-100 dark:hover:bg-primary-hover' => $isPrimary,
                            'border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['cta'] }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to explore your data?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Sign up for free and connect your first database in under a minute.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Get started free
                </a>
            @endif
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                Talk to us
            </a>
        </div>
    </section>
</x-layouts::public>

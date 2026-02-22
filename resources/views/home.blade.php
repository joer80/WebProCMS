<x-layouts::public title="GetRows — Query Your Data in Seconds" description="GetRows turns your database into an instant, shareable workspace. Write a query, explore results, and share insights — no SQL expertise required.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Now in public beta</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Query your data<br>in seconds.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            GetRows turns your database into an instant, shareable workspace. Write a query, explore results, and share insights — no SQL expertise required.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    Get started free
                </a>
            @endif
            <a href="{{ route('about') }}" class="inline-block px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                Learn more
            </a>
        </div>
    </section>

    {{-- Hero visual --}}
    <div class="mb-24 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 lg:p-10">
        <div class="bg-[#FDFDFC] dark:bg-[#0a0a0a] rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 font-mono text-sm text-[#706f6c] dark:text-[#A1A09A] space-y-1">
            <p><span class="text-[#f53003] dark:text-[#FF4433]">SELECT</span> id, name, email, created_at</p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">FROM</span> users</p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">WHERE</span> created_at &gt;= <span class="text-green-600 dark:text-green-400">'2025-01-01'</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">ORDER BY</span> created_at <span class="text-[#f53003] dark:text-[#FF4433]">DESC</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">LIMIT</span> <span class="text-green-600 dark:text-green-400">50</span>;</p>
        </div>
        <div class="mt-4 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-md overflow-hidden text-xs">
            <div class="grid grid-cols-4 bg-[#f5f5f3] dark:bg-[#1D1D1B] text-[#706f6c] dark:text-[#A1A09A] font-medium px-4 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <span>id</span><span>name</span><span>email</span><span>created_at</span>
            </div>
            @foreach ([
                [1, 'Alice Martin', 'alice@example.com', '2025-03-12'],
                [2, 'Bob Chen', 'bob@example.com', '2025-03-09'],
                [3, 'Carol Adams', 'carol@example.com', '2025-03-07'],
            ] as $row)
                <div class="grid grid-cols-4 px-4 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-b-0 text-primary dark:text-primary-surface">
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ $row[0] }}</span>
                    <span>{{ $row[1] }}</span>
                    <span class="truncate">{{ $row[2] }}</span>
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ $row[3] }}</span>
                </div>
            @endforeach
            <div class="px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] bg-[#f5f5f3] dark:bg-[#1D1D1B]">
                50 rows returned in 12ms
            </div>
        </div>
    </div>

    {{-- Intro --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">What is GetRows?</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Your database, without the friction.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                Most database tools are built for database administrators. GetRows is built for everyone else — marketers, product managers, analysts, and developers who just need answers fast.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                Connect your database in under a minute, write plain SQL or use our guided query builder, and share results with your team as a live link or exported file.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ([
                ['No setup required', 'Connect a database and you\'re ready to go.'],
                ['Runs in the cloud', 'Nothing to install. Works in any browser.'],
                ['Team-friendly', 'Share queries and results with a single link.'],
                ['Always fast', 'Optimised query execution for big datasets.'],
            ] as [$title, $desc])
                {{-- Example of card without using Flux --}}
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
                    'title' => 'Instant Query Editor',
                    'description' => 'Write SQL with intelligent autocomplete, syntax highlighting, and real-time error hints. Results appear as you type.',
                ],
                [
                    'title' => 'Visual Query Builder',
                    'description' => 'Not a SQL expert? Build queries by clicking — filter, sort, group, and join tables with a drag-and-drop interface.',
                ],
                [
                    'title' => 'Live Shared Results',
                    'description' => 'Share a query result as a live link. Recipients always see up-to-date data without needing database access.',
                ],
                [
                    'title' => 'Export Anywhere',
                    'description' => 'Download results as CSV, JSON, or Excel. Push directly to Google Sheets, Notion, or your favourite BI tool.',
                ],
                [
                    'title' => 'Query History',
                    'description' => 'Every query is saved automatically. Search, replay, and fork previous queries in one click.',
                ],
                [
                    'title' => 'Role-based Access',
                    'description' => 'Keep sensitive tables safe. Grant read-only access to specific users or teams without touching database permissions.',
                ],
            ] as $service)
                {{-- Example of card without using Flux --}}
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $service['title'] }}</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $service['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Flux card example --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Built for speed</span>
            <h2 class="text-3xl font-semibold leading-tight">From connection to insight in minutes.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                ['Connect', 'circle-stack', 'Paste your database URL and GetRows handles the rest. Supports PostgreSQL, MySQL, and SQLite.'],
                ['Query', 'code-bracket', 'Write SQL or use the visual builder. Autocomplete and inline hints keep you moving fast.'],
                ['Share', 'share', 'Send a live link to your results. No logins, no exports — just a URL that always shows fresh data.'],
            ] as [$title, $icon, $desc])
                <flux:card class="flex flex-col gap-3">
                    <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                    <flux:heading size="lg">{{ $title }}</flux:heading>
                    <flux:text>{{ $desc }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to get started?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Join thousands of teams already using GetRows to make smarter, faster decisions with their data.
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

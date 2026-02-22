<x-layouts::public title="Instant Query Editor — GetRows" description="Write SQL with intelligent autocomplete, syntax highlighting, and real-time error hints. Results appear as you type — no page reload, no waiting.">
    {{-- Breadcrumb --}}
    <nav class="mb-8 text-sm text-[#706f6c] dark:text-[#A1A09A]">
        <a href="{{ route('services') }}" class="hover:text-primary dark:hover:text-primary-surface transition-colors">Services</a>
        <span class="mx-2">/</span>
        <span>Instant Query Editor</span>
    </nav>

    {{-- Hero --}}
    <section class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-md bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center shrink-0">
                <flux:icon name="code-bracket" class="text-[#706f6c] dark:text-[#A1A09A]" />
            </div>
            <span class="text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A]">Feature</span>
        </div>
        <h1 class="text-4xl font-semibold leading-tight mb-4">Instant Query Editor</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl">
            Write SQL with intelligent autocomplete, syntax highlighting, and real-time error hints. Results appear as you type — no page reload, no waiting.
        </p>
    </section>

    {{-- Editor preview --}}
    <div class="mb-16 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 lg:p-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
            <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
            <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
            <span class="ml-2 text-xs text-[#706f6c] dark:text-[#A1A09A]">query_1.sql</span>
        </div>
        <div class="bg-[#FDFDFC] dark:bg-[#0a0a0a] rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 font-mono text-sm space-y-1 mb-4">
            <p><span class="text-[#f53003] dark:text-[#FF4433]">SELECT</span> <span class="text-[#706f6c] dark:text-[#A1A09A]">u.id, u.name, u.email,</span></p>
            <p class="pl-8"><span class="text-[#706f6c] dark:text-[#A1A09A]">COUNT(o.id) AS</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">order_count,</span></p>
            <p class="pl-8"><span class="text-[#706f6c] dark:text-[#A1A09A]">SUM(o.total) AS</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">lifetime_value</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">FROM</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">users u</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">LEFT JOIN</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">orders o</span> <span class="text-[#f53003] dark:text-[#FF4433]">ON</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">o.user_id = u.id</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">WHERE</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">u.created_at &gt;=</span> <span class="text-green-600 dark:text-green-400">'2025-01-01'</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">GROUP BY</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">u.id</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">ORDER BY</span> <span class="text-[#1b1b18] dark:text-[#EDEDEC]">lifetime_value</span> <span class="text-[#f53003] dark:text-[#FF4433]">DESC</span></p>
            <p><span class="text-[#f53003] dark:text-[#FF4433]">LIMIT</span> <span class="text-green-600 dark:text-green-400">25</span><span class="text-[#1b1b18] dark:text-[#EDEDEC]">;</span></p>
        </div>
        <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-md overflow-hidden text-xs">
            <div class="grid grid-cols-5 bg-[#f5f5f3] dark:bg-[#1D1D1B] text-[#706f6c] dark:text-[#A1A09A] font-medium px-4 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <span>id</span><span>name</span><span>email</span><span>order_count</span><span>lifetime_value</span>
            </div>
            @foreach ([
                [1, 'Alice Martin', 'alice@example.com', 42, '$8,240.00'],
                [2, 'Bob Chen', 'bob@example.com', 31, '$5,910.50'],
                [3, 'Carol Adams', 'carol@example.com', 28, '$4,620.00'],
            ] as $row)
                <div class="grid grid-cols-5 px-4 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-b-0 text-primary dark:text-primary-surface">
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ $row[0] }}</span>
                    <span>{{ $row[1] }}</span>
                    <span class="truncate text-[#706f6c] dark:text-[#A1A09A]">{{ $row[2] }}</span>
                    <span>{{ $row[3] }}</span>
                    <span class="text-green-600 dark:text-green-400">{{ $row[4] }}</span>
                </div>
            @endforeach
            <div class="px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] bg-[#f5f5f3] dark:bg-[#1D1D1B]">
                25 rows returned in 8ms
            </div>
        </div>
    </div>

    {{-- Features --}}
    <section class="mb-24">
        <div class="mb-10">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">What's included</span>
            <h2 class="text-3xl font-semibold leading-tight">Built for speed and precision.</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                [
                    'icon' => 'sparkles',
                    'title' => 'Syntax highlighting',
                    'description' => 'Keywords, strings, numbers, and identifiers are coloured instantly so your query is always easy to read — even complex multi-table statements.',
                ],
                [
                    'icon' => 'bolt',
                    'title' => 'Autocomplete with schema awareness',
                    'description' => 'The editor knows your table names, column names, and data types. Suggestions appear as you type so you spend less time looking things up.',
                ],
                [
                    'icon' => 'exclamation-triangle',
                    'title' => 'Real-time error detection',
                    'description' => 'Syntax errors are underlined the moment you make them. Hover for a plain-English explanation of exactly what went wrong and how to fix it.',
                ],
                [
                    'icon' => 'rectangle-group',
                    'title' => 'Multi-tab support',
                    'description' => 'Open multiple queries in tabs and switch between them instantly. Each tab has its own editor state and results — perfect for comparing approaches.',
                ],
                [
                    'icon' => 'arrows-pointing-in',
                    'title' => 'Query formatting',
                    'description' => 'One click to auto-format and indent your SQL. Paste in a messy query from anywhere and the editor will tidy it up in milliseconds.',
                ],
                [
                    'icon' => 'command-line',
                    'title' => 'Keyboard-first workflow',
                    'description' => 'Run queries with Cmd/Ctrl+Enter, switch tabs, format code, and navigate results without lifting your hands from the keyboard.',
                ],
            ] as $feature)
                <div class="flex gap-4">
                    <div class="w-9 h-9 rounded-md bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center shrink-0 mt-0.5">
                        <flux:icon :name="$feature['icon']" class="text-[#706f6c] dark:text-[#A1A09A]" />
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">{{ $feature['title'] }}</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $feature['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Who it's for --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-start">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Who it's for</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Fast answers for everyone on your team.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                The Instant Query Editor is designed to feel familiar to SQL veterans while remaining approachable for anyone who's written a basic SELECT statement.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                Developers, analysts, and product managers use it daily to answer one-off questions without opening a terminal or waiting for an engineer.
            </p>
        </div>
        <div class="grid gap-4">
            @foreach ([
                ['Developers', 'Prototype queries fast, debug schema issues, and explore unfamiliar databases without switching tools.'],
                ['Data analysts', 'Write complex aggregations and window functions with schema-aware autocomplete at your side.'],
                ['Product managers', 'Answer your own data questions with guided error hints — no SQL experience required to get started.'],
            ] as [$role, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-5">
                    <p class="font-semibold text-sm mb-1">{{ $role }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Try the editor for free.</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Connect your database and run your first query in under a minute. No credit card required.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Get started free
                </a>
            @endif
            <a href="{{ route('services') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                See all features
            </a>
        </div>
    </section>
</x-layouts::public>

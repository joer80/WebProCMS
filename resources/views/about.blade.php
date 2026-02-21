<x-layouts::public title="About Us">
    {{-- Hero --}}
    <section class="mb-16 text-center">
        <h1 class="text-4xl font-semibold leading-tight mb-4">About GetRows</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl mx-auto">
            We're on a mission to make data accessible, actionable, and beautiful for everyone — no spreadsheet degree required.
        </p>
    </section>

    {{-- Story --}}
    <section class="mb-16 grid lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-2xl font-semibold mb-3">Our Story</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                GetRows was founded in 2023 by a small team of developers and data enthusiasts who were tired of wrestling with slow, bloated tools. We believed there had to be a better way to query, visualize, and share data.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                What started as an internal tool quickly grew into something the whole team relied on daily. So we decided to share it with the world.
            </p>
        </div>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 flex items-center justify-center aspect-video">
            <span class="text-[#706f6c] dark:text-[#A1A09A] text-sm">[ Company photo placeholder ]</span>
        </div>
    </section>

    {{-- Values --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-8 text-center">What We Believe</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                <h3 class="font-semibold mb-2">Simplicity First</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    Powerful tools don't have to be complicated. We strip away the noise so you can focus on your data.
                </p>
            </div>
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                <h3 class="font-semibold mb-2">Built to Scale</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    Whether you're querying a handful of rows or millions, GetRows handles it without breaking a sweat.
                </p>
            </div>
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                <h3 class="font-semibold mb-2">Your Data, Your Rules</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    Privacy and security are non-negotiable. We never sell your data or share it with third parties.
                </p>
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-8 text-center">Meet the Team</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                ['name' => 'Alex Rivera', 'role' => 'Co-founder & CEO'],
                ['name' => 'Jordan Lee', 'role' => 'Co-founder & CTO'],
                ['name' => 'Morgan Chen', 'role' => 'Head of Design'],
            ] as $member)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] mx-auto mb-4"></div>
                    <p class="font-semibold">{{ $member['name'] }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ $member['role'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts::public>

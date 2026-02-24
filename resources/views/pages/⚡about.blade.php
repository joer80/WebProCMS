<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Learn about WebProCMS — our story, mission, and the team behind the CMS built for web professionals. Founded to make content management simple, powerful, and enjoyable.'])] #[Title('About Us — WebProCMS')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="mb-16 text-center">
        <h1 class="text-4xl font-semibold leading-tight mb-4">About WebProCMS</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl mx-auto">
            We're on a mission to make content management simple, powerful, and enjoyable for web professionals everywhere — no bloat, no compromise.
        </p>
    </section>

    {{-- Story --}}
    <section class="mb-16 grid lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-2xl font-semibold mb-3">Our Story</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                WebProCMS was founded by web developers and agency owners who spent years fighting inflexible, bloated CMS platforms. We believed there was a better way — one that respected developers, empowered content creators, and didn't require a weekend of configuration just to launch a site.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                What started as an internal tool for our own agency quickly became something we wanted to share with the world. WebProCMS is built for the web professionals who deserve better.
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
                <h3 class="font-semibold mb-2">Developer-first</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    We never hide complexity behind magic. Our architecture is clean, documented, and built for developers who want to understand and extend every part of the system.
                </p>
            </div>
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                <h3 class="font-semibold mb-2">Flexible by Design</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    Your content structure is unique. WebProCMS adapts to your needs — not the other way around. Custom types, custom fields, custom everything.
                </p>
            </div>
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                <h3 class="font-semibold mb-2">Your Data, Your Rules</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal">
                    We believe in open architectures and data sovereignty. Export everything, host anywhere, and integrate with whatever tools your team relies on.
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
                ['name' => 'Morgan Chen', 'role' => 'Head of Product'],
            ] as $member)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] mx-auto mb-4"></div>
                    <p class="font-semibold">{{ $member['name'] }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ $member['role'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>

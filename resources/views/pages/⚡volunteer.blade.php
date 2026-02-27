<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Give your time and skills to make a real difference. Explore volunteer opportunities and sign up today.'])] #[Title('Volunteer')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Get involved</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Your time is<br>our greatest asset.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            Whether you can give two hours a week or two days a month, your skills and energy help us deliver programs that transform lives in our community.
        </p>
        <a href="#apply" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
            Apply to volunteer
        </a>
    </section>

    {{-- Opportunities --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Open opportunities</span>
            <h2 class="text-3xl font-semibold leading-tight">Find the right role for you.</h2>
        </div>
        <div class="space-y-4">
            @foreach ([
                ['After-School Tutor', 'Education', 'Weekday afternoons', 'Help K-12 students with homework, reading, and math in our community learning centre.'],
                ['Food Pantry Assistant', 'Food programs', 'Flexible (weekdays & weekends)', 'Sort donations, pack food boxes, and assist families during pantry hours.'],
                ['Community Garden Helper', 'Environment', 'Saturdays', 'Maintain our urban garden that provides fresh produce to local families.'],
                ['Event Coordinator', 'Fundraising', 'Occasional', 'Help plan and run our fundraising galas, 5K runs, and awareness events.'],
                ['Tech Support Volunteer', 'Digital inclusion', 'Flexible', 'Assist seniors and low-income residents with computers, tablets, and internet access.'],
            ] as [$title, $category, $schedule, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h3 class="font-semibold">{{ $title }}</h3>
                                <span class="text-xs px-2 py-0.5 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A]">{{ $category }}</span>
                            </div>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">
                                <flux:icon name="clock" class="inline size-3.5 -mt-0.5 mr-1" />
                                {{ $schedule }}
                            </p>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $desc }}</p>
                        </div>
                        <a href="#apply" class="shrink-0 px-4 py-1.5 text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm transition-colors">
                            Apply
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Application form placeholder --}}
    <section id="apply" class="mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-10">
                <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Sign up</span>
                <h2 class="text-3xl font-semibold leading-tight">Start your application.</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A] mt-3">Fill out the form and our volunteer coordinator will be in touch within two business days.</p>
            </div>
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 space-y-4">
                @foreach ([['Name', 'text', 'Your full name'], ['Email', 'email', 'you@example.com'], ['Phone', 'tel', '+1 (512) 555-0100']] as [$label, $type, $placeholder])
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ $label }}</label>
                        <input type="{{ $type }}" placeholder="{{ $placeholder }}" class="w-full px-3 py-2 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-transparent placeholder-[#A1A09A] dark:placeholder-[#706f6c] focus:outline-none focus:border-[#706f6c] dark:focus:border-[#A1A09A] transition-colors" />
                    </div>
                @endforeach
                <div>
                    <label class="block text-sm font-medium mb-1.5">Areas of interest</label>
                    <select class="w-full px-3 py-2 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-transparent text-[#706f6c] dark:text-[#A1A09A] focus:outline-none focus:border-[#706f6c] dark:focus:border-[#A1A09A] transition-colors">
                        <option>Select a role...</option>
                        <option>After-School Tutor</option>
                        <option>Food Pantry Assistant</option>
                        <option>Community Garden Helper</option>
                        <option>Event Coordinator</option>
                        <option>Tech Support Volunteer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Why do you want to volunteer?</label>
                    <textarea rows="4" placeholder="Tell us a little about yourself..." class="w-full px-3 py-2 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-transparent placeholder-[#A1A09A] dark:placeholder-[#706f6c] focus:outline-none focus:border-[#706f6c] dark:focus:border-[#A1A09A] transition-colors resize-none"></textarea>
                </div>
                <button class="w-full px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                    Submit application
                </button>
            </div>
        </div>
    </section>
</div>

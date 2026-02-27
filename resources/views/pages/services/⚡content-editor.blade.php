<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Write and edit with a powerful block-based editor. Rich text, images, embeds, and custom blocks — all in one clean interface. No developer needed.'])] #[Title('Visual Content Editor — WebProCMS')] class extends Component {
}; ?>

<div>
    {{-- Breadcrumb --}}
    <nav class="mb-8 text-sm text-[#706f6c] dark:text-[#A1A09A]">
        <a href="{{ route('services') }}" class="hover:text-primary dark:hover:text-primary-surface transition-colors">Services</a>
        <span class="mx-2">/</span>
        <span>Visual Content Editor</span>
    </nav>

    {{-- Hero --}}
    <section class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-md bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center shrink-0">
                <flux:icon name="document-text" class="text-[#706f6c] dark:text-[#A1A09A]" />
            </div>
            <span class="text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A]">Feature</span>
        </div>
        <h1 class="text-4xl font-semibold leading-tight mb-4">Visual Content Editor</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl">
            Write and edit with a powerful block-based editor. Rich text, images, video embeds, code blocks, and custom components — all from one clean, distraction-free interface.
        </p>
    </section>

    {{-- Editor preview --}}
    <div class="mb-16 bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 lg:p-8">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                <div class="w-3 h-3 rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                <span class="ml-2 text-xs text-[#706f6c] dark:text-[#A1A09A]">Editing: Getting Started with WebProCMS</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2.5 py-1 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A]">Draft</span>
                <span class="text-xs px-2.5 py-1 bg-primary text-primary-foreground rounded">Publish</span>
            </div>
        </div>
        <div class="flex items-center gap-1.5 mb-4 pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            @foreach (['B', 'I', 'U', 'H₁', 'H₂', '"', 'Link', '{ }', '⋯'] as $tool)
                <span class="text-xs px-1.5 py-0.5 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] font-mono select-none">{{ $tool }}</span>
            @endforeach
        </div>
        <div class="space-y-3">
            <p class="text-xl font-semibold text-primary dark:text-primary-surface">Getting Started with WebProCMS</p>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-relaxed">
                WebProCMS is designed for web professionals who need a flexible, powerful platform without unnecessary complexity. This guide walks you through publishing your first piece of content.
            </p>
            <div class="h-20 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded border border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-center">
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">+ Add image block</span>
            </div>
            <div class="bg-[#f5f5f3] dark:bg-[#0a0a0a] rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] p-3 font-mono text-xs text-[#706f6c] dark:text-[#A1A09A]">
                <span class="text-[#f53003] dark:text-[#FF4433]">// Code block</span><br>
                <span class="text-primary dark:text-primary-surface">const</span> cms = <span class="text-green-600 dark:text-green-400">'WebProCMS'</span>;
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
                    'icon' => 'squares-2x2',
                    'title' => 'Block-based editing',
                    'description' => 'Build content from composable blocks — paragraphs, headings, images, quotes, code, embeds, and custom types. Reorder anything with a drag and drop.',
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Version history',
                    'description' => 'Every save creates a version. Compare changes side-by-side, roll back to any previous state, or restore content that was accidentally deleted.',
                ],
                [
                    'icon' => 'bolt',
                    'title' => 'Schema-aware fields',
                    'description' => 'Create custom field types for your content models — text, rich text, select, relationship, media, and more. The editor adapts to your data structure.',
                ],
                [
                    'icon' => 'rectangle-group',
                    'title' => 'Multi-tab editing',
                    'description' => 'Work on multiple posts at once without losing your place. Each tab saves its own editor state and scroll position between sessions.',
                ],
                [
                    'icon' => 'eye',
                    'title' => 'Live preview',
                    'description' => 'See exactly how your content will look before it goes live, rendered in any template or layout. Preview on desktop, tablet, and mobile.',
                ],
                [
                    'icon' => 'command-line',
                    'title' => 'Keyboard-first workflow',
                    'description' => 'Navigate and format content without lifting your hands from the keyboard. Familiar shortcuts — Cmd/Ctrl+B, Cmd/Ctrl+K, and more — work exactly as expected.',
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
            <h2 class="text-3xl font-semibold leading-tight mb-4">A great editing experience for everyone on your team.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                The Visual Content Editor is powerful enough for developers to extend and simple enough for non-technical editors to use without training.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                Content editors, developers, and agency teams use it daily to produce and publish web content without friction or back-and-forth.
            </p>
        </div>
        <div class="grid gap-4">
            @foreach ([
                ['Content editors', 'Format and structure content exactly the way you want — rich text, media, and custom blocks — no developer required.'],
                ['Developers', 'Extend the editor with custom block types and field definitions that map directly to your content model.'],
                ['Agencies', 'Deliver a consistent, polished editing experience to every client, regardless of the project\'s complexity or content structure.'],
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
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Start editing content today.</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Create your account and publish your first piece of content in under a minute. No credit card required.
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
</div>

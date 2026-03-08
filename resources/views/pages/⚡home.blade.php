<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'WebProCMS is a clean, powerful content management platform built for web professionals, developers, and agencies who demand more from their tools.'])] #[Title('WebProCMS — Build, Manage, and Publish Without Limits')] class extends Component {
    /** @var \Illuminate\Database\Eloquent\Collection<int, Post> */
    public $recentPosts;

    public function mount(): void
    {
        $this->recentPosts = Post::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->limit(3)
            ->get();
    }
}; ?>
<div>{{-- ROW:start:hero-centered:6JIvVk --}}
<x-dl.section slug="hero-centered:6JIvVk"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <x-dl.heading slug="hero-centered:6JIvVk" prefix="headline" default="Build Something Amazing"
                default-tag="h1"
                default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight" />
            <x-dl.subheadline slug="hero-centered:6JIvVk" prefix="subheadline" default="Describe your product or service here. Keep it concise and focused on the value you deliver to customers."
                default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400" />
            <x-dl.buttons slug="hero-centered:6JIvVk"
                default-wrapper-classes="mt-8 flex flex-wrap gap-4"
                default-primary-label="Start Free Trial"
                default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
                default-secondary-label="Watch Demo →"
                default-secondary-classes="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors" />
        </div>
        <x-dl.media slug="hero-centered:6JIvVk"
            default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center"
            default-image-classes="w-full h-full object-cover"
            default-image="https://placehold.co/1200x675" />
</x-dl.section>

{{-- ROW:end:hero-centered:6JIvVk --}}

{{-- ROW:start:content-two-column:XnaRdV --}}
<x-dl.section slug="content-two-column:XnaRdV"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="content-two-column:XnaRdV" prefix="headline" default="Our Story"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.subheadline slug="content-two-column:XnaRdV" prefix="body" tag="div" default="<p>We started with a simple question: why is software so hard to use? After years of watching teams struggle with disconnected tools, we set out to build something better.</p><p>Our platform was born in 2020 from the frustration of trying to coordinate a distributed team across a dozen different apps. We knew there had to be a better way — and we built it.</p><p>Today, we're proud to serve thousands of teams worldwide, from scrappy two-person startups to global enterprises with thousands of employees. Our mission hasn't changed: make great software that works for everyone.</p>"
        default-classes="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300 leading-relaxed space-y-4" />
</x-dl.section>

{{-- ROW:end:content-two-column:XnaRdV --}}

{{-- ROW:start:cta-banner:uWIg5r --}}
<x-dl.section slug="cta-banner:uWIg5r"
    default-section-classes="bg-primary py-section-banner px-6 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.heading slug="cta-banner:uWIg5r" prefix="headline" default="Ready to Get Started?"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="cta-banner:uWIg5r" prefix="subheadline" default="Join thousands of satisfied customers today."
            default-classes="mt-4 text-lg text-white/80" />
        <x-dl.buttons slug="cta-banner:uWIg5r"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            default-secondary-label="Talk to Sales"
            default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
{{-- ROW:end:cta-banner:uWIg5r --}}
</div>

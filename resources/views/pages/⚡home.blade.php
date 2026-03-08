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
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-[3fr_2fr] gap-6 items-stretch">
    <x-dl.heading slug="content-two-column:XnaRdV" prefix="headline" default="Complete Care When You Need It Most"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center md:col-span-2" />
    <x-dl.subheadline slug="content-two-column:XnaRdV" prefix="subheadline" default="Whether you're treating an urgent issue, need diagnostic testing, or focusing on long-term wellness, we provide comprehensive services all under one roof."
        default-classes="mt-4 mb-4 text-lg text-zinc-500 dark:text-zinc-400 text-center max-w-3xl mx-auto md:col-span-2" />

    {{-- Left: large featured card with background image and overlay text --}}
    @php $hasFeaturedImg = (bool) content('content-two-column:XnaRdV', 'featured_image_image', ''); @endphp
    <x-dl.wrapper slug="content-two-column:XnaRdV" prefix="featured_card"
        default-classes="rounded-card overflow-hidden bg-zinc-900 dark:bg-zinc-800 relative flex flex-col justify-end min-h-80">
        <x-dl.image slug="content-two-column:XnaRdV" prefix="featured_image"
            default-wrapper-classes="absolute inset-0"
            default-image-classes="w-full h-full object-cover opacity-50" />
        @if (!$hasFeaturedImg)
            <div class="absolute inset-0 flex items-center justify-center text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        @endif
        <x-dl.group slug="content-two-column:XnaRdV" prefix="featured_content"
            default-classes="relative z-10 p-8">
            <x-dl.heading slug="content-two-column:XnaRdV" prefix="featured_title" default="Illness &amp; Injury"
                default-tag="h3"
                default-classes="font-heading text-2xl font-bold text-white mb-3" />
            <x-dl.subheadline slug="content-two-column:XnaRdV" prefix="featured_desc" default="Walk in for fast treatment of common illnesses and injuries. Most major insurance plans welcome, plus affordable self-pay options."
                default-classes="text-zinc-300 text-base leading-relaxed" />
        </x-dl.group>
    </x-dl.wrapper>

    {{-- Right: three smaller cards stacked vertically --}}
    <x-dl.grid slug="content-two-column:XnaRdV" prefix="cards"
        default-grid-classes="grid gap-4"
        default-items='[{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit."},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management."},{"image":"","image_alt":"Occupational Medicine","title":"Occupational Medicine","desc":"Comprehensive occupational health services tailored for your workforce."}]'>
        @dlItems('content-two-column:XnaRdV', 'cards', $cards, '[{"image":"","image_alt":"X-Rays & Lab Work","title":"X-Rays & Lab Work","desc":"On-site diagnostics enable fast answers and treatment, all in one convenient visit."},{"image":"","image_alt":"Wellness & Preventive Care","title":"Wellness & Preventive Care","desc":"Proactive health services from preventive screenings to weight management."},{"image":"","image_alt":"Occupational Medicine","title":"Occupational Medicine","desc":"Comprehensive occupational health services tailored for your workforce."}]')
        @foreach ($cards as $card)
            @php $cardImg = !empty($card['image']) ? (str_starts_with($card['image'], 'http') ? $card['image'] : \Illuminate\Support\Facades\Storage::url($card['image'])) : null; @endphp
            <x-dl.card slug="content-two-column:XnaRdV" prefix="card"
                default-classes="rounded-card overflow-hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm flex">
                <x-dl.wrapper slug="content-two-column:XnaRdV" prefix="card_image_wrapper"
                    default-classes="w-28 shrink-0 overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                    @if ($cardImg)
                        <img src="{{ $cardImg }}" alt="{{ $card['image_alt'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="content-two-column:XnaRdV" prefix="card_body" default-classes="flex-1 p-4 flex flex-col justify-center">
                    <x-dl.wrapper slug="content-two-column:XnaRdV" prefix="card_title" tag="h3"
                        default-classes="font-heading text-base font-bold text-zinc-900 dark:text-white mb-1">
                        {{ $card['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="content-two-column:XnaRdV" prefix="card_desc" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $card['desc'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>

    <x-dl.buttons slug="content-two-column:XnaRdV"
        default-wrapper-classes="md:col-span-2 mt-2 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Our Locations"
        default-primary-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Learn More"
        default-secondary-classes="px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors" />
</x-dl.section>
{{-- ROW:end:content-two-column:XnaRdV --}}

{{-- ROW:start:cta-banner:uWIg5r --}}
<x-dl.section slug="cta-banner:uWIg5r"
    default-section-classes="py-section-banner px-6 bg-primary"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
    <div>
        <x-dl.heading slug="cta-banner:uWIg5r" prefix="headline" default="Find a Location Near You"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-white" />
        <x-dl.subheadline slug="cta-banner:uWIg5r" prefix="subheadline" default="With locations across the region, quality care is always close by. Walk in today or book online."
            default-classes="mt-3 text-white/80" />
    </div>
    <x-dl.buttons slug="cta-banner:uWIg5r"
        default-wrapper-classes="flex flex-wrap items-center justify-start md:justify-end gap-4"
        default-primary-label="Our Locations"
        default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
        default-secondary-label="Contact Us"
        default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
{{-- ROW:end:cta-banner:uWIg5r --}}
</div>

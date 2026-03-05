<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-split:JkIwN1 --}}
<x-dl.section slug="hero-split:JkIwN1"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <x-dl.heading slug="hero-split:JkIwN1" prefix="headline" default="Build Something Amazing"
                default-tag="h1"
                default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight" />
            <x-dl.subheadline slug="hero-split:JkIwN1" prefix="subheadline" default="Describe your product or service here. Keep it concise and focused on the value you deliver to customers."
                default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400" />
            <x-dl.buttons slug="hero-split:JkIwN1"
                default-wrapper-classes="mt-8 flex flex-wrap gap-4"
                default-primary-label="Start Free Trial"
                default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
                default-secondary-label="Watch Demo →"
                default-secondary-classes="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors" />
        </div>
        <x-dl.media slug="hero-split:JkIwN1"
            default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center"
            default-image-classes="w-full h-full object-cover" />
</x-dl.section>
{{-- ROW:end:hero-split:JkIwN1 --}}

{{-- ROW:start:section:yaZ2S0 --}}
<x-dl.section slug="section:yaZ2S0"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">

{{-- @dl-item:heading:headline:Heading --}}
<x-dl.heading slug="section:yaZ2S0" prefix="headline" default="Your Heading"
    default-tag="h2"
    default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
{{-- /@dl-item --}}

{{-- @dl-item:subheadline:subheadline:Subheadline --}}
<x-dl.subheadline slug="section:yaZ2S0" prefix="subheadline" default="Supporting text goes here."
    default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
{{-- /@dl-item --}}


{{-- @dl-item:buttons:buttons:Buttons --}}
<x-dl.buttons slug="section:yaZ2S0"
    default-wrapper-classes="mt-8 flex flex-wrap items-center gap-4"
    default-primary-label="Get Started"
    default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
    default-secondary-label="Learn More"
    default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors" />
{{-- /@dl-item --}}
</x-dl.section>
{{-- ROW:end:section:yaZ2S0 --}}

{{-- ROW:start:faqs-accordion:TbpLHg --}}
<x-dl.section slug="faqs-accordion:TbpLHg"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="faqs-accordion:TbpLHg" prefix="header_wrapper"
        default-classes="text-center mb-16">
        <x-dl.heading slug="faqs-accordion:TbpLHg" prefix="headline" default="Frequently Asked Questions"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="faqs-accordion:TbpLHg" prefix="subheadline" default="Can't find what you're looking for?"
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.link slug="faqs-accordion:TbpLHg" prefix="contact"
            default-label="Contact us"
            default-url="/contact"
            default-classes="text-primary underline" />
    </x-dl.wrapper>
    <x-dl.accordion slug="faqs-accordion:TbpLHg" prefix="faqs"
        default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."}]'>
        @dlItems('faqs-accordion:TbpLHg', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."}]')
        @foreach ($faqs as $i => $faq)
            <x-dl.accordion-item slug="faqs-accordion:TbpLHg" prefix="faq_item" :index="$i"
                question="{{ $faq['question'] }}"
                default-classes="py-5"
                default-button-classes="w-full flex items-center justify-between text-left"
                default-question-classes="text-base font-semibold text-zinc-900 dark:text-white"
                default-chevron-classes="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
                default-answer-classes="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                {{ $faq['answer'] }}
            </x-dl.accordion-item>
        @endforeach
    </x-dl.accordion>
</x-dl.section>
{{-- ROW:end:faqs-accordion:TbpLHg --}}

{{-- ROW:start:gallery-grid:buM1HF --}}
@php $galleryLightboxEnabled = content('gallery-grid:buM1HF', 'toggle_lightbox', '1') === '1'; @endphp
<x-dl.section slug="gallery-grid:buM1HF"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto"
    x-data="{
        lightboxEnabled: {{ $galleryLightboxEnabled ? 'true' : 'false' }},
        lightboxOpen: false,
        lightboxIndex: 0,
        images: [],
        init() {
            const imgs = this.$el.querySelectorAll('img[data-lightbox-src]');
            this.images = Array.from(imgs).map(img => ({
                src: img.dataset.lightboxSrc,
                alt: img.alt,
                caption: img.dataset.lightboxCaption || ''
            }));
        },
        open(imgEl) {
            if (!this.lightboxEnabled || !imgEl) return;
            const idx = this.images.findIndex(i => i.src === imgEl.dataset.lightboxSrc);
            if (idx !== -1) { this.lightboxIndex = idx; this.lightboxOpen = true; }
        },
        prev() { this.lightboxIndex = (this.lightboxIndex - 1 + this.images.length) % this.images.length; },
        next() { this.lightboxIndex = (this.lightboxIndex + 1) % this.images.length; }
    }">
    <x-dl.wrapper slug="gallery-grid:buM1HF" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="gallery-grid:buM1HF" prefix="headline" default="Our Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="gallery-grid:buM1HF" prefix="subheadline" default="A glimpse into our work and culture."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('gallery-grid:buM1HF', 'images', $galleryImages, '[{"image":"","alt":"Photo 1","caption":""},{"image":"","alt":"Photo 2","caption":""},{"image":"","alt":"Photo 3","caption":""},{"image":"","alt":"Photo 4","caption":""},{"image":"","alt":"Photo 5","caption":""},{"image":"","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="gallery-grid:buM1HF" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-4"
        default-items='[{"image":"","alt":"Photo 1","caption":""},{"image":"","alt":"Photo 2","caption":""},{"image":"","alt":"Photo 3","caption":""},{"image":"","alt":"Photo 4","caption":""},{"image":"","alt":"Photo 5","caption":""},{"image":"","alt":"Photo 6","caption":""}]'>
        @foreach ($galleryImages as $img)
            <x-dl.card slug="gallery-grid:buM1HF" prefix="gallery_item"
                default-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square"
                @click="open($el.querySelector('img[data-lightbox-src]'))"
                x-bind:class="lightboxEnabled && $el.querySelector('img[data-lightbox-src]') ? 'cursor-zoom-in' : ''">
                @if ($img['image'])
                    <img src="{{ Storage::url($img['image']) }}" alt="{{ $img['alt'] }}"
                        data-lightbox-src="{{ Storage::url($img['image']) }}"
                        data-lightbox-caption="{{ $img['caption'] ?? '' }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>

    {{-- Lightbox overlay — functional UI, teleported to body to escape stacking contexts --}}
    <template x-teleport="body">
        <div x-show="lightboxOpen" style="display:none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="lightboxOpen = false"
            @keydown.escape.window="lightboxOpen = false"
            @keydown.arrow-left.window="if (lightboxOpen) prev()"
            @keydown.arrow-right.window="if (lightboxOpen) next()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90">

            {{-- Close --}}
            <button @click="lightboxOpen = false" aria-label="Close"
                class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Prev --}}
            <button x-show="images.length > 1" @click="prev()" aria-label="Previous"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Image --}}
            <div class="flex items-center justify-center px-16 max-h-[90vh]">
                <template x-for="(img, i) in images" :key="i">
                    <div x-show="lightboxIndex === i" class="flex flex-col items-center gap-3">
                        <img :src="img.src" :alt="img.alt" class="max-h-[82vh] max-w-[85vw] object-contain">
                        <p x-show="img.caption" x-text="img.caption" class="text-white/70 text-sm text-center"></p>
                    </div>
                </template>
            </div>

            {{-- Next --}}
            <button x-show="images.length > 1" @click="next()" aria-label="Next"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            {{-- Counter --}}
            <div x-show="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2">
                <span x-text="`${lightboxIndex + 1} / ${images.length}`" class="text-white/50 text-sm tabular-nums"></span>
            </div>
        </div>
    </template>
</x-dl.section>
{{-- ROW:end:gallery-grid:buM1HF --}}

{{-- ROW:start:features-grid:86pxJq --}}
<x-dl.section slug="features-grid:86pxJq"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="features-grid:86pxJq" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="features-grid:86pxJq" prefix="headline" default="Everything You Need"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="features-grid:86pxJq" prefix="subheadline" default="Powerful features designed to help you succeed."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="features-grid:86pxJq" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]'>
        @dlItems('features-grid:86pxJq', 'features', $features, '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="features-grid:86pxJq" prefix="feature_card"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                <x-dl.icon slug="features-grid:86pxJq" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-8" />
                <x-dl.wrapper slug="features-grid:86pxJq" prefix="feature_title" tag="h3"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="features-grid:86pxJq" prefix="feature_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    {{ $feature['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
{{-- ROW:end:features-grid:86pxJq --}}

{{-- ROW:start:cta-banner:ctq8Be --}}
<x-dl.section slug="cta-banner:ctq8Be"
    default-section-classes="bg-primary py-section-banner px-6 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.heading slug="cta-banner:ctq8Be" prefix="headline" default="Ready to Get Started?"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="cta-banner:ctq8Be" prefix="subheadline" default="Join thousands of satisfied customers today."
            default-classes="mt-4 text-lg text-white/80" />
        <x-dl.buttons slug="cta-banner:ctq8Be"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            default-secondary-label="Talk to Sales"
            default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
{{-- ROW:end:cta-banner:ctq8Be --}}
</div>

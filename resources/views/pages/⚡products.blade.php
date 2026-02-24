<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Browse our full product catalog. Quality items for every need, with fast shipping and easy returns.'])] #[Title('Products')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="py-16 lg:py-20">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Our catalog</span>
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <h1 class="text-4xl lg:text-5xl font-semibold leading-tight">Products.</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] max-w-sm leading-normal">Handpicked quality. Free shipping on orders over $75.</p>
        </div>
    </section>

    {{-- Category filters --}}
    <section class="mb-8">
        <div class="flex items-center gap-2 flex-wrap">
            @foreach (['All', 'New Arrivals', 'Best Sellers', 'Sale'] as $filter)
                <button class="px-4 py-1.5 text-sm rounded-sm border {{ $loop->first ? 'border-[#1b1b18] dark:border-[#EDEDEC] bg-[#1b1b18] dark:bg-[#EDEDEC] text-[#FDFDFC] dark:text-[#1b1b18]' : 'border-[#e3e3e0] dark:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] hover:border-[#706f6c]' }} transition-colors">
                    {{ $filter }}
                </button>
            @endforeach
        </div>
    </section>

    {{-- Product grid --}}
    <section class="mb-24">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ([
                ['Wireless Headphones Pro', '$149.00', 'Premium sound with active noise cancellation and 30-hour battery life.', 'New'],
                ['Leather Desk Pad', '$59.00', 'Full-grain leather surface with non-slip base. 36" × 18".', null],
                ['Mechanical Keyboard', '$189.00', 'Compact 75% layout with hot-swap switches and USB-C connection.', 'Best Seller'],
                ['Ergonomic Mouse', '$79.00', 'Vertical design reduces wrist strain. Works on any surface.', null],
                ['Cable Management Kit', '$24.00', 'Keep your desk tidy with velcro straps, clips, and a cable box.', 'Sale'],
                ['Monitor Stand', '$89.00', 'Solid aluminium riser with USB-A and USB-C passthrough ports.', null],
            ] as [$name, $price, $desc, $badge])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden flex flex-col">
                    <div class="h-48 bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center relative">
                        <flux:icon name="photo" class="size-12 text-[#e3e3e0] dark:text-[#3E3E3A]" />
                        @if ($badge)
                            <span class="absolute top-3 left-3 text-xs font-semibold px-2 py-0.5 rounded bg-[#1b1b18] dark:bg-[#EDEDEC] text-[#FDFDFC] dark:text-[#1b1b18]">{{ $badge }}</span>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-semibold mb-1">{{ $name }}</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4 flex-1">{{ $desc }}</p>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <span class="font-semibold">{{ $price }}</span>
                            <button class="px-3 py-1.5 text-xs font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm transition-colors">
                                Add to cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Value props --}}
    <section class="mb-8 grid sm:grid-cols-3 gap-6">
        @foreach ([
            ['truck', 'Free shipping', 'On all orders over $75. Delivered in 2–5 business days.'],
            ['arrow-path', 'Easy returns', '30-day hassle-free returns. No questions asked.'],
            ['shield-check', 'Secure checkout', 'Your payment information is always encrypted and safe.'],
        ] as [$icon, $title, $desc])
            <div class="flex items-start gap-4">
                <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A] shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-sm mb-1">{{ $title }}</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $desc }}</p>
                </div>
            </div>
        @endforeach
    </section>
</div>

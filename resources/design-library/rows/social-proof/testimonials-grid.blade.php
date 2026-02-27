{{--
@name Social Proof - Testimonials Grid
@description Three-column testimonial cards with quotes and author details.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Loved by Thousands</h2>
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Here's what our customers have to say.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([['quote' => 'This product completely transformed how our team works. I can\'t imagine going back.', 'name' => 'Sarah Johnson', 'role' => 'CEO at Acme Co'], ['quote' => 'The best investment we\'ve made this year. Setup was a breeze and support is incredible.', 'name' => 'Mark Davis', 'role' => 'CTO at BuildIt'], ['quote' => 'Our productivity has doubled since we started using this. Highly recommended.', 'name' => 'Lisa Chen', 'role' => 'Product Manager at TechCorp']] as $testimonial)
                <div class="p-6 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex text-primary mb-4">
                        @for ($i = 0; $i < 5; $i++) ★ @endfor
                    </div>
                    <p class="text-zinc-700 dark:text-zinc-300 leading-relaxed italic">"{{ $testimonial['quote'] }}"</p>
                    <div class="mt-6 flex items-center gap-3">
                        <div class="size-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $testimonial['name'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $testimonial['role'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

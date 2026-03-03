{{--
@name Social Proof - Testimonials Grid
@description Three-column testimonial cards with quotes and author details.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Loved by Thousands"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Here's what our customers have to say."
                default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="testimonials"
            default-grid-classes="grid md:grid-cols-3 gap-6"
            default-items='[{"quote":"This product completely transformed how our team works. I can\u0027t imagine going back.","name":"Sarah Johnson","role":"CEO at Acme Co"},{"quote":"The best investment we\u0027ve made this year. Setup was a breeze and support is incredible.","name":"Mark Davis","role":"CTO at BuildIt"},{"quote":"Our productivity has doubled since we started using this. Highly recommended.","name":"Lisa Chen","role":"Product Manager at TechCorp"}]'>
            @dlItems('__SLUG__', 'testimonials', $testimonials, '[{"quote":"This product completely transformed how our team works. I can\u0027t imagine going back.","name":"Sarah Johnson","role":"CEO at Acme Co"},{"quote":"The best investment we\u0027ve made this year. Setup was a breeze and support is incredible.","name":"Mark Davis","role":"CTO at BuildIt"},{"quote":"Our productivity has doubled since we started using this. Highly recommended.","name":"Lisa Chen","role":"Product Manager at TechCorp"}]')
            @foreach ($testimonials as $testimonial)
                <x-dl.card slug="__SLUG__" prefix="card"
                    default-classes="p-6 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                    <x-dl.wrapper slug="__SLUG__" prefix="stars"
                        default-classes="flex text-primary mb-4">
                        ★★★★★
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="quote" tag="p"
                        default-classes="text-zinc-700 dark:text-zinc-300 leading-relaxed italic">
                        "{{ $testimonial['quote'] }}"
                    </x-dl.wrapper>
                    <x-dl.group slug="__SLUG__" prefix="author_row"
                        default-classes="mt-6 flex items-center gap-3">
                        <x-dl.wrapper slug="__SLUG__" prefix="avatar"
                            default-classes="size-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </x-dl.wrapper>
                        <div>
                            <x-dl.wrapper slug="__SLUG__" prefix="author_name"
                                default-classes="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $testimonial['name'] }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="author_role"
                                default-classes="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $testimonial['role'] }}
                            </x-dl.wrapper>
                        </div>
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
</x-dl.section>

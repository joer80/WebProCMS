{{--
@name Social Proof - Stats
@description Large statistics row highlighting key business metrics.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-14">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="The Numbers Speak for Themselves"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="stats"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-8 text-center"
        default-items='[{"number":"10K+","label":"Happy Customers"},{"number":"99.9%","label":"Uptime"},{"number":"50M","label":"Events/Day"},{"number":"4.9/5","label":"Average Rating"}]'>
        @dlItems('__SLUG__', 'stats', $stats, '[{"number":"10K+","label":"Happy Customers"},{"number":"99.9%","label":"Uptime"},{"number":"50M","label":"Events/Day"},{"number":"4.9/5","label":"Average Rating"}]')
        @foreach ($stats as $stat)
            <x-dl.card slug="__SLUG__" prefix="stat_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6">
                <x-dl.wrapper slug="__SLUG__" prefix="stat_number"
                    default-classes="text-5xl font-black text-primary">
                    {{ $stat['number'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="stat_label" tag="p"
                    default-classes="mt-2 text-sm text-zinc-400 font-medium">
                    {{ $stat['label'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

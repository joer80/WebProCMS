{{--
@name Contact - Form
@description Two-column contact section with info on the left and a form card on the right.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto grid lg:grid-cols-2 gap-12 items-start">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Get in Touch"
            default-tag="h1"
            default-classes="text-4xl font-semibold leading-tight mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="description" default="Have a question or just want to say hello? Fill out the form and we'll get back to you within one business day."
            default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-8" />
        <x-dl.grid slug="__SLUG__" prefix="contact_items"
            default-grid-classes="space-y-6 text-sm"
            default-items='[{"label":"Email","value":""},{"label":"Phone","value":""},{"label":"Office","value":""},{"label":"Hours","value":""}]'>
            <x-dl.business-info-toggle slug="__SLUG__" />
            @dlItems('__SLUG__', 'contact_items', $contactItems, '[{"label":"Email","value":""},{"label":"Phone","value":""},{"label":"Office","value":""},{"label":"Hours","value":""}]')
            @php
            if (content('__SLUG__', 'toggle_use_business_info', '1') === '1') {
                $contactItems = array_values(array_filter([
                    config('business.email') ? ['label' => 'Email', 'value' => config('business.email')] : null,
                    config('business.phone') ? ['label' => 'Phone', 'value' => config('business.phone')] : null,
                    (config('business.address_street') || config('business.address_city_state_zip'))
                        ? ['label' => 'Office', 'value' => implode(', ', array_filter([config('business.address_street'), config('business.address_city_state_zip')]))]
                        : null,
                    config('business.hours') ? ['label' => 'Hours', 'value' => config('business.hours')] : null,
                ]));
            }
            @endphp
            @foreach ($contactItems as $contactItem)
                <x-dl.card slug="__SLUG__" prefix="contact_item" default-classes="">
                    <x-dl.wrapper slug="__SLUG__" prefix="item_label" tag="p"
                        default-classes="font-semibold mb-1">{{ $contactItem['label'] }}</x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="item_value" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400">{{ $contactItem['value'] }}</x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </div>
    <x-dl.form-select slug="__SLUG__" />
</x-dl.section>

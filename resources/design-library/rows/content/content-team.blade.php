{{--
@name Team Member Grid
@description Team member cards with photo, name, role, and optional bio.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Meet the Team"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="The people behind the product."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="members"
        default-grid-classes="grid sm:grid-cols-2 md:grid-cols-4 gap-8"
        default-items='[{"name":"Alex Johnson","role":"CEO & Co-Founder","bio":"Visionary leader with 15 years in SaaS.","image":"https://placehold.co/300x300"},{"name":"Sam Rivera","role":"CTO & Co-Founder","bio":"Full-stack engineer and systems architect.","image":"https://placehold.co/300x300"},{"name":"Morgan Lee","role":"Head of Design","bio":"Creating beautiful and intuitive experiences.","image":"https://placehold.co/300x300"},{"name":"Jordan Davis","role":"Head of Growth","bio":"Data-driven marketer focused on results.","image":"https://placehold.co/300x300"}]'>
        @dlItems('__SLUG__', 'members', $members, '[{"name":"Alex Johnson","role":"CEO & Co-Founder","bio":"Visionary leader with 15 years in SaaS.","image":"https://placehold.co/300x300"},{"name":"Sam Rivera","role":"CTO & Co-Founder","bio":"Full-stack engineer and systems architect.","image":"https://placehold.co/300x300"},{"name":"Morgan Lee","role":"Head of Design","bio":"Creating beautiful and intuitive experiences.","image":"https://placehold.co/300x300"},{"name":"Jordan Davis","role":"Head of Growth","bio":"Data-driven marketer focused on results.","image":"https://placehold.co/300x300"}]')
        @foreach ($members as $member)
            @php $memberImg = $member['image'] ? (str_starts_with($member['image'], 'http') ? $member['image'] : \Illuminate\Support\Facades\Storage::url($member['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="member_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="text-center">
                <x-dl.wrapper slug="__SLUG__" prefix="member_photo_wrapper"
                    default-classes="rounded-full overflow-hidden mx-auto mb-4 size-24 bg-zinc-100 dark:bg-zinc-800">
                    @if ($memberImg)
                        <img src="{{ $memberImg }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-2xl font-bold">{{ substr($member['name'], 0, 1) }}</div>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_name" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white">
                    {{ $member['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_role" tag="p"
                    default-classes="text-sm text-primary mt-0.5">
                    {{ $member['role'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_bio" tag="p"
                    default-classes="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $member['bio'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>

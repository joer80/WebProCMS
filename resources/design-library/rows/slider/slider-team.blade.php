{{--
@name Slider - Team Slider
@description Auto-advancing team member slider with photo and bio.
@sort 80
--}}
@dlItems('__SLUG__', 'members', $members, '[{"name":"Alex Johnson","role":"CEO","bio":"15 years in SaaS, passionate about building products that change how people work.","image":"https://placehold.co/400x400"},{"name":"Sam Rivera","role":"CTO","bio":"Full-stack engineer with a love for distributed systems and developer experience.","image":"https://placehold.co/400x400"},{"name":"Morgan Lee","role":"Head of Design","bio":"Award-winning designer who believes great UX is invisible.","image":"https://placehold.co/400x400"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-4xl mx-auto"
    x-data="{
        current: 0,
        total: {{ count($members) }},
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
        }
    }"
    x-init="autoPlay()">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Leadership Team"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="slides_wrapper"
        default-classes="text-center min-h-[260px]">
        @foreach ($members as $i => $member)
            @php $memberImg = $member['image'] ? (str_starts_with($member['image'], 'http') ? $member['image'] : \Illuminate\Support\Facades\Storage::url($member['image'])) : null; @endphp
            <div x-show="current === {{ $i }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <x-dl.wrapper slug="__SLUG__" prefix="member_photo_wrapper"
                    default-classes="size-24 rounded-full overflow-hidden mx-auto mb-5 bg-zinc-200 dark:bg-zinc-700">
                    @if ($memberImg)
                        <img src="{{ $memberImg }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 font-bold text-2xl">{{ substr($member['name'], 0, 1) }}</div>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_name"
                    default-classes="text-xl font-bold text-zinc-900 dark:text-white">
                    {{ $member['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_role" tag="p"
                    default-classes="text-primary font-medium mt-1">
                    {{ $member['role'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="member_bio" tag="p"
                    default-classes="mt-4 text-zinc-500 dark:text-zinc-400 max-w-md mx-auto leading-relaxed">
                    {{ $member['bio'] }}
                </x-dl.wrapper>
            </div>
        @endforeach
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="dots_wrapper"
        default-classes="flex items-center justify-center gap-2 mt-8">
        @foreach ($members as $i => $member)
            <x-dl.wrapper slug="__SLUG__" prefix="dot" tag="button"
                default-classes="h-2 rounded-full transition-all duration-300"
                @click="current = {{ $i }}"
                :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'">
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>

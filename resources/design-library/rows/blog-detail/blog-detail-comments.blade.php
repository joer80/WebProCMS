{{--
@name Blog Detail - Comments
@description Comments section with comment cards and a leave a comment form.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Comments"
        default-tag="h2"
        default-classes="font-heading text-2xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.grid slug="__SLUG__" prefix="comments"
        default-grid-classes="space-y-6 mb-12"
        default-items='[{"author":"Jane Doe","date":"January 5, 2025","body":"This was incredibly helpful! I have been struggling with this concept for a while and you explained it perfectly."},{"author":"John Smith","date":"January 6, 2025","body":"Great article. Would love to see a follow-up post exploring some of the edge cases you mentioned."}]'>
        @dlItems('__SLUG__', 'comments', $comments, '[{"author":"Jane Doe","date":"January 5, 2025","body":"This was incredibly helpful! I have been struggling with this concept for a while and you explained it perfectly."},{"author":"John Smith","date":"January 6, 2025","body":"Great article. Would love to see a follow-up post exploring some of the edge cases you mentioned."}]')
        @foreach ($comments as $comment)
            <x-dl.card slug="__SLUG__" prefix="comment_card"
            data-editor-item-index="{{ $loop->index }}"
                default-classes="p-5 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                <x-dl.group slug="__SLUG__" prefix="comment_header"
                    default-classes="flex items-center gap-3 mb-3">
                    <x-dl.wrapper slug="__SLUG__" prefix="comment_avatar"
                        default-classes="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold">
                        {{ substr($comment['author'], 0, 1) }}
                    </x-dl.wrapper>
                    <div>
                        <x-dl.wrapper slug="__SLUG__" prefix="comment_author" tag="span"
                            default-classes="font-semibold text-sm text-zinc-900 dark:text-white">
                            {{ $comment['author'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="comment_date" tag="p"
                            default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $comment['date'] }}
                        </x-dl.wrapper>
                    </div>
                </x-dl.group>
                <x-dl.wrapper slug="__SLUG__" prefix="comment_body" tag="p"
                    default-classes="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    {{ $comment['body'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.wrapper slug="__SLUG__" prefix="comment_form_wrapper"
        default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700">
        <x-dl.heading slug="__SLUG__" prefix="form_heading" default="Leave a Comment"
            default-tag="h3"
            default-classes="font-heading text-lg font-semibold text-zinc-900 dark:text-white mb-6" />
        <x-dl.wrapper slug="__SLUG__" prefix="comment_form" tag="form"
            default-classes="space-y-4">
            <x-dl.wrapper slug="__SLUG__" prefix="name_input" tag="input"
                type="text"
                placeholder="Your name"
                default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
            <x-dl.wrapper slug="__SLUG__" prefix="email_input" tag="input"
                type="email"
                placeholder="Your email"
                default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
            <x-dl.wrapper slug="__SLUG__" prefix="comment_textarea" tag="textarea"
                rows="4"
                placeholder="Write your comment..."
                default-classes="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"></x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="submit_button" tag="button"
                type="submit"
                default-classes="px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Post Comment
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>

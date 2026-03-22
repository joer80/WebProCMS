<div
    x-data="{
        open: false,
        fieldKey: null,
        fieldType: null,
        size: 'medium',
        insert() {
            const sentences = [
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi aliquip ex ea commodo consequat.',
                'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur.',
                'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.',
                'Ut labore et dolore magnam aliquam quaerat voluptatem, ut enim ad minima veniam.',
                'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.',
                'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat.',
                'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates.',
                'Itaque earum rerum hic tenetur a sapiente delectus ut aut reiciendis voluptatibus maiores alias consequatur.',
            ];
            const counts = { sentence: 1, short: 3, medium: 6, long: 12 };
            const count = counts[this.size] || 6;
            const isRich = this.fieldType === 'richtext';
            let content = '';
            if (isRich) {
                if (this.size === 'long') {
                    const half = Math.ceil(count / 2);
                    content = '<p>' + sentences.slice(0, half).join(' ') + '</p><p>' + sentences.slice(half, count).join(' ') + '</p>';
                } else {
                    content = '<p>' + sentences.slice(0, count).join(' ') + '</p>';
                }
            } else {
                content = sentences.slice(0, count).join(' ');
            }
            window.dispatchEvent(new CustomEvent('ai-content-generated', { detail: { fieldKey: this.fieldKey, content }, bubbles: true }));
            this.open = false;
        }
    }"
    @open-lorem-ipsum.window="open = true; fieldKey = $event.detail.fieldKey; fieldType = $event.detail.fieldType; size = $event.detail.defaultSize || 'medium';"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    @keydown.escape.window="open = false"
>
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    <div class="relative w-full max-w-sm bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Insert Lorem Ipsum</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Choose how much placeholder text to insert.</p>
            </div>
            <button type="button" @click="open = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                <flux:icon name="x-mark" class="size-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3 block">Amount</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="size = 'sentence'" :class="size === 'sentence' ? 'border-primary bg-primary/5 text-primary' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-primary/50'" class="rounded-lg border px-4 py-3 text-left transition-colors">
                    <span class="block text-sm font-medium">One sentence</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">~10 words</span>
                </button>
                <button type="button" @click="size = 'short'" :class="size === 'short' ? 'border-primary bg-primary/5 text-primary' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-primary/50'" class="rounded-lg border px-4 py-3 text-left transition-colors">
                    <span class="block text-sm font-medium">A few sentences</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">~40 words</span>
                </button>
                <button type="button" @click="size = 'medium'" :class="size === 'medium' ? 'border-primary bg-primary/5 text-primary' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-primary/50'" class="rounded-lg border px-4 py-3 text-left transition-colors">
                    <span class="block text-sm font-medium">One paragraph</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">~80 words</span>
                </button>
                <button type="button" @click="size = 'long'" :class="size === 'long' ? 'border-primary bg-primary/5 text-primary' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-primary/50'" class="rounded-lg border px-4 py-3 text-left transition-colors">
                    <span class="block text-sm font-medium">Two paragraphs</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">~160 words</span>
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-zinc-200 dark:border-zinc-700">
            <button type="button" @click="open = false" class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Cancel</button>
            <button type="button" @click="insert()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors">
                <flux:icon name="document-text" class="size-4" />
                Insert
            </button>
        </div>
    </div>
</div>

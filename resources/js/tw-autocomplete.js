/**
 * Tailwind CSS class autocomplete for the design editor.
 * Generates the class list at runtime and registers an Alpine component.
 */

const COLORS = ['slate','gray','zinc','neutral','stone','red','orange','amber','yellow','lime','green','emerald','teal','cyan','sky','blue','indigo','violet','purple','fuchsia','pink','rose'];
const SHADES = [50,100,200,300,400,500,600,700,800,900,950];
// Custom theme tokens from resources/css/app.css @theme block.
// Update this list whenever you add or rename tokens there, then run npm run build.
const CUSTOM_COLORS = ['primary','primary-hover','primary-foreground','primary-surface','accent','accent-content','accent-foreground'];
const SPACING = ['0','px','0.5','1','1.5','2','2.5','3','3.5','4','5','6','7','8','9','10','11','12','14','16','20','24','28','32','36','40','44','48','52','56','60','64','72','80','96'];
const MODIFIERS = ['dark:','sm:','md:','lg:','xl:','2xl:','hover:','focus:','active:','disabled:','focus-within:','focus-visible:','group-hover:','group-focus:','peer-hover:','first:','last:','odd:','even:','motion-reduce:','print:'];

function buildTwClasses() {
    const cls = [];

    // — Custom theme colors —
    const COLOR_PREFIXES = ['text','bg','border','ring','ring-offset','outline','shadow','from','via','to','divide','decoration','caret','accent','placeholder'];
    CUSTOM_COLORS.forEach(c => COLOR_PREFIXES.forEach(p => cls.push(`${p}-${c}`)));

    // — Text —
    cls.push('text-xs','text-sm','text-base','text-lg','text-xl','text-2xl','text-3xl','text-4xl','text-5xl','text-6xl','text-7xl','text-8xl','text-9xl');
    cls.push('text-left','text-center','text-right','text-justify','text-start','text-end');
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`text-${c}-${s}`)));
    cls.push('text-white','text-black','text-transparent','text-current','text-inherit');

    // — Font —
    cls.push('font-thin','font-extralight','font-light','font-normal','font-medium','font-semibold','font-bold','font-extrabold','font-black');
    cls.push('font-sans','font-serif','font-mono','font-heading');
    cls.push('italic','not-italic');

    // — Text decoration / transform —
    cls.push('underline','overline','line-through','no-underline');
    cls.push('uppercase','lowercase','capitalize','normal-case');

    // — Tracking / leading —
    cls.push('tracking-tighter','tracking-tight','tracking-normal','tracking-wide','tracking-wider','tracking-widest');
    cls.push('leading-none','leading-tight','leading-snug','leading-normal','leading-relaxed','leading-loose');
    [3,4,5,6,7,8,9,10].forEach(n => cls.push(`leading-${n}`));

    // — Whitespace / overflow text —
    cls.push('whitespace-normal','whitespace-nowrap','whitespace-pre','whitespace-pre-line','whitespace-pre-wrap','whitespace-break-spaces');
    cls.push('truncate','text-ellipsis','text-clip');
    cls.push('break-all','break-words','break-keep','break-normal');
    [1,2,3,4,5,6].forEach(n => cls.push(`line-clamp-${n}`));
    cls.push('line-clamp-none');

    // — Background —
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`bg-${c}-${s}`)));
    cls.push('bg-white','bg-black','bg-transparent','bg-current','bg-inherit');
    cls.push('bg-cover','bg-contain','bg-auto');
    cls.push('bg-center','bg-top','bg-bottom','bg-left','bg-right','bg-left-top','bg-right-top','bg-left-bottom','bg-right-bottom');
    cls.push('bg-fixed','bg-local','bg-scroll','bg-repeat','bg-no-repeat','bg-repeat-x','bg-repeat-y');
    cls.push('bg-gradient-to-t','bg-gradient-to-tr','bg-gradient-to-r','bg-gradient-to-br','bg-gradient-to-b','bg-gradient-to-bl','bg-gradient-to-l','bg-gradient-to-tl');
    COLORS.forEach(c => SHADES.forEach(s => {
        cls.push(`from-${c}-${s}`,`via-${c}-${s}`,`to-${c}-${s}`);
    }));
    cls.push('from-transparent','via-transparent','to-transparent','from-white','via-white','to-white','from-black','via-black','to-black');

    // — Border —
    cls.push('border','border-0','border-2','border-4','border-8');
    ['t','r','b','l','x','y'].forEach(s => {
        cls.push(`border-${s}`);
        ['0','2','4','8'].forEach(w => cls.push(`border-${s}-${w}`));
    });
    cls.push('border-solid','border-dashed','border-dotted','border-double','border-none');
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`border-${c}-${s}`)));
    cls.push('border-white','border-black','border-transparent');
    cls.push('divide-x','divide-y','divide-x-reverse','divide-y-reverse');
    ['0','2','4','8'].forEach(w => cls.push(`divide-x-${w}`,`divide-y-${w}`));
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`divide-${c}-${s}`)));
    cls.push('divide-solid','divide-dashed','divide-dotted','divide-none');

    // — Rounded —
    const roundedSizes = ['','sm','md','lg','xl','2xl','3xl','full','none','card'];
    roundedSizes.forEach(r => cls.push(r ? `rounded-${r}` : 'rounded'));
    ['t','r','b','l','tl','tr','bl','br','ss','se','es','ee'].forEach(side => {
        roundedSizes.forEach(r => cls.push(r ? `rounded-${side}-${r}` : `rounded-${side}`));
    });

    // — Ring —
    cls.push('ring','ring-0','ring-1','ring-2','ring-4','ring-8','ring-inset');
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`ring-${c}-${s}`)));
    cls.push('ring-white','ring-black','ring-transparent');
    ['0','1','2','4','8'].forEach(w => cls.push(`ring-offset-${w}`));
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`ring-offset-${c}-${s}`)));

    // — Shadow —
    cls.push('shadow-sm','shadow','shadow-md','shadow-lg','shadow-xl','shadow-2xl','shadow-inner','shadow-none','shadow-card');
    COLORS.forEach(c => [500,600,700].forEach(s => cls.push(`shadow-${c}-${s}`)));

    // — Outline —
    cls.push('outline-none','outline','outline-dashed','outline-dotted','outline-double');
    ['0','1','2','4','8'].forEach(w => cls.push(`outline-${w}`));
    COLORS.forEach(c => SHADES.forEach(s => cls.push(`outline-${c}-${s}`)));

    // — Padding —
    ['p','px','py','pt','pr','pb','pl'].forEach(p => {
        SPACING.forEach(s => cls.push(`${p}-${s}`));
        cls.push(`${p}-auto`,`${p}-section`);
    });

    // — Margin —
    ['m','mx','my','mt','mr','mb','ml'].forEach(p => {
        SPACING.forEach(s => cls.push(`${p}-${s}`));
        cls.push(`${p}-auto`,`${p}-section`);
        SPACING.filter(s => s !== '0').forEach(s => cls.push(`-${p}-${s}`));
    });

    // — Gap / space —
    ['gap','gap-x','gap-y'].forEach(p => SPACING.forEach(s => cls.push(`${p}-${s}`)));
    ['space-x','space-y'].forEach(p => {
        SPACING.forEach(s => cls.push(`${p}-${s}`));
        cls.push(`${p}-reverse`);
    });

    // — Width —
    SPACING.forEach(s => cls.push(`w-${s}`));
    ['auto','full','screen','svw','dvw','fit','max','min','1/2','1/3','2/3','1/4','3/4','1/5','2/5','3/5','4/5','1/6','5/6'].forEach(s => cls.push(`w-${s}`));

    // — Height —
    SPACING.forEach(s => cls.push(`h-${s}`));
    ['auto','full','screen','svh','dvh','fit','max','min'].forEach(s => cls.push(`h-${s}`));

    // — Size (w+h) —
    SPACING.forEach(s => cls.push(`size-${s}`));
    ['auto','full','fit','max','min'].forEach(s => cls.push(`size-${s}`));

    // — Max/min width —
    ['max-w','min-w'].forEach(p => {
        SPACING.forEach(s => cls.push(`${p}-${s}`));
        ['none','full','screen','min','max','fit','xs','sm','md','lg','xl','2xl','3xl','4xl','5xl','6xl','7xl','prose'].forEach(s => cls.push(`${p}-${s}`));
    });

    // — Max/min height —
    ['max-h','min-h'].forEach(p => {
        SPACING.forEach(s => cls.push(`${p}-${s}`));
        ['none','full','screen','svh','dvh','fit','max','min'].forEach(s => cls.push(`${p}-${s}`));
    });

    // — Display —
    cls.push('block','inline-block','inline','flex','inline-flex','grid','inline-grid','contents','hidden','table','table-cell','table-row','table-caption','flow-root','list-item','sr-only','not-sr-only');

    // — Flexbox —
    cls.push('flex-row','flex-row-reverse','flex-col','flex-col-reverse');
    cls.push('flex-wrap','flex-nowrap','flex-wrap-reverse');
    cls.push('flex-1','flex-auto','flex-initial','flex-none');
    cls.push('grow','grow-0','shrink','shrink-0');
    cls.push('items-start','items-end','items-center','items-baseline','items-stretch');
    cls.push('justify-start','justify-end','justify-center','justify-between','justify-around','justify-evenly','justify-stretch','justify-normal');
    cls.push('self-auto','self-start','self-end','self-center','self-stretch','self-baseline');
    cls.push('content-normal','content-center','content-start','content-end','content-between','content-around','content-evenly','content-baseline','content-stretch');
    cls.push('place-items-start','place-items-end','place-items-center','place-items-baseline','place-items-stretch');
    cls.push('place-content-center','place-content-start','place-content-end','place-content-between','place-content-around','place-content-evenly','place-content-stretch');

    // — Grid —
    [1,2,3,4,5,6,7,8,9,10,11,12].forEach(n => {
        cls.push(`grid-cols-${n}`,`col-span-${n}`,`col-start-${n}`,`col-end-${n}`);
        cls.push(`grid-rows-${n}`,`row-span-${n}`,`row-start-${n}`,`row-end-${n}`);
    });
    cls.push('grid-cols-none','grid-cols-subgrid','grid-rows-none','grid-rows-subgrid');
    cls.push('col-auto','col-span-full','row-auto','row-span-full');
    cls.push('auto-cols-auto','auto-cols-min','auto-cols-max','auto-cols-fr');
    cls.push('auto-rows-auto','auto-rows-min','auto-rows-max','auto-rows-fr');

    // — Position —
    cls.push('static','fixed','absolute','relative','sticky');
    SPACING.forEach(s => {
        cls.push(`top-${s}`,`right-${s}`,`bottom-${s}`,`left-${s}`,`inset-${s}`);
        cls.push(`-top-${s}`,`-right-${s}`,`-bottom-${s}`,`-left-${s}`);
    });
    cls.push('top-auto','right-auto','bottom-auto','left-auto','inset-auto','inset-x-0','inset-y-0','inset-x-auto','inset-y-auto');
    [0,10,20,30,40,50].forEach(n => cls.push(`z-${n}`));
    cls.push('z-auto');

    // — Overflow —
    ['overflow','overflow-x','overflow-y'].forEach(p => {
        ['auto','hidden','clip','visible','scroll'].forEach(v => cls.push(`${p}-${v}`));
    });

    // — Opacity —
    [0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100].forEach(n => cls.push(`opacity-${n}`));

    // — Transition —
    cls.push('transition','transition-none','transition-all','transition-colors','transition-opacity','transition-shadow','transition-transform');
    [75,100,150,200,300,500,700,1000].forEach(n => cls.push(`duration-${n}`,`delay-${n}`));
    cls.push('ease-linear','ease-in','ease-out','ease-in-out','delay-0');

    // — Transform —
    cls.push('transform','transform-none','transform-cpu','transform-gpu');
    [0,50,75,90,95,100,105,110,125,150].forEach(n => cls.push(`scale-${n}`,`scale-x-${n}`,`scale-y-${n}`));
    [0,1,2,3,6,12,45,90,180].forEach(n => { cls.push(`rotate-${n}`); if (n) cls.push(`-rotate-${n}`); });
    SPACING.forEach(s => {
        cls.push(`translate-x-${s}`,`translate-y-${s}`,`-translate-x-${s}`,`-translate-y-${s}`);
    });
    ['1/2','1/3','2/3','1/4','3/4','full'].forEach(s => {
        cls.push(`translate-x-${s}`,`translate-y-${s}`,`-translate-x-${s}`,`-translate-y-${s}`);
    });
    [1,2,3,6,12].forEach(n => { cls.push(`skew-x-${n}`,`skew-y-${n}`,`-skew-x-${n}`,`-skew-y-${n}`); });
    cls.push('origin-center','origin-top','origin-top-right','origin-right','origin-bottom-right','origin-bottom','origin-bottom-left','origin-left','origin-top-left');

    // — Object fit/position —
    cls.push('object-contain','object-cover','object-fill','object-none','object-scale-down');
    cls.push('object-center','object-top','object-right','object-bottom','object-left','object-right-top','object-left-top','object-right-bottom','object-left-bottom');

    // — Aspect ratio —
    cls.push('aspect-auto','aspect-square','aspect-video');

    // — Cursor —
    cls.push('cursor-auto','cursor-default','cursor-pointer','cursor-wait','cursor-text','cursor-move','cursor-help','cursor-not-allowed','cursor-none','cursor-context-menu','cursor-crosshair','cursor-grab','cursor-grabbing','cursor-zoom-in','cursor-zoom-out','cursor-alias','cursor-copy','cursor-no-drop');

    // — Interactivity —
    cls.push('select-none','select-text','select-all','select-auto');
    cls.push('pointer-events-none','pointer-events-auto');
    cls.push('resize-none','resize-y','resize-x','resize');
    cls.push('appearance-none','appearance-auto');
    cls.push('will-change-auto','will-change-scroll','will-change-contents','will-change-transform');

    // — Visibility —
    cls.push('visible','invisible','collapse');

    // — Misc —
    cls.push('antialiased','subpixel-antialiased');
    cls.push('box-border','box-content');
    cls.push('float-left','float-right','float-none','clear-left','clear-right','clear-both','clear-none');
    cls.push('list-none','list-disc','list-decimal','list-inside','list-outside');
    cls.push('table-auto','table-fixed','border-collapse','border-separate');
    cls.push('container','mx-auto');
    cls.push('backdrop-blur-none','backdrop-blur-sm','backdrop-blur','backdrop-blur-md','backdrop-blur-lg','backdrop-blur-xl','backdrop-blur-2xl','backdrop-blur-3xl');
    cls.push('blur-none','blur-sm','blur','blur-md','blur-lg','blur-xl','blur-2xl','blur-3xl');
    cls.push('grayscale','grayscale-0','invert','invert-0','sepia','sepia-0');
    cls.push('brightness-0','brightness-50','brightness-75','brightness-90','brightness-95','brightness-100','brightness-105','brightness-110','brightness-125','brightness-150','brightness-200');
    cls.push('contrast-0','contrast-50','contrast-75','contrast-100','contrast-125','contrast-150','contrast-200');
    cls.push('saturate-0','saturate-50','saturate-100','saturate-150','saturate-200');

    // — Caret / accent / placeholder / decoration —
    COLORS.forEach(c => SHADES.forEach(s => {
        cls.push(`caret-${c}-${s}`,`accent-${c}-${s}`,`placeholder-${c}-${s}`,`decoration-${c}-${s}`);
    }));
    cls.push('decoration-auto','decoration-from-font','decoration-solid','decoration-double','decoration-dotted','decoration-dashed','decoration-wavy');
    ['0','1','2','4','8'].forEach(w => cls.push(`decoration-${w}`));
    cls.push('underline-offset-auto','underline-offset-0','underline-offset-1','underline-offset-2','underline-offset-4','underline-offset-8');

    return cls;
}

const TW_CLASSES = buildTwClasses();

/**
 * Register the Tailwind autocomplete Alpine component.
 *
 * Timing: app.js is a deferred module — it runs AFTER Alpine has already
 * initialized synchronously via @fluxScripts. That means alpine:init has
 * already fired by the time this code runs, so a listener would never trigger.
 *
 * The fix: call Alpine.data() directly if Alpine is already loaded (the common
 * case), and keep the listener only as a fallback for environments where
 * Alpine loads later.
 *
 * This is safe because the x-data="twAutocomplete(...)" elements are only
 * injected into the DOM by Livewire AFTER a user interaction (opening a row),
 * which always happens after this module has executed.
 */
function registerTwAutocomplete() {
    Alpine.data('twAutocomplete', (fieldKey) => ({
        open: false,
        suggestions: [],
        activeIndex: -1,
        dropdownStyle: {},

        suggest(e) {
            const ta = e.target;
            const pos = ta.selectionStart;
            const before = ta.value.slice(0, pos);
            const wordStart = before.lastIndexOf(' ') + 1;
            const word = before.slice(wordStart);

            if (word.length < 2) { this.close(); return; }

            // Strip known modifier prefix before searching
            const modifier = MODIFIERS.find(m => word.startsWith(m)) ?? '';
            const search = word.slice(modifier.length);

            if (!search) { this.close(); return; }

            const matches = TW_CLASSES.filter(c => c.startsWith(search)).slice(0, 50);
            this.suggestions = modifier ? matches.map(c => modifier + c) : matches;

            if (this.suggestions.length > 0) {
                // Use fixed positioning so the dropdown escapes any overflow:hidden parents
                const rect = this.$refs.input.getBoundingClientRect();
                this.dropdownStyle = {
                    position: 'fixed',
                    top: (rect.bottom + 4) + 'px',
                    left: rect.left + 'px',
                    width: rect.width + 'px',
                    zIndex: 9999,
                };
                this.open = true;
                this.activeIndex = 0;
            } else {
                this.close();
            }
        },

        handleKey(e) {
            if (!this.open) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.activeIndex = Math.min(this.activeIndex + 1, this.suggestions.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.activeIndex = Math.max(this.activeIndex - 1, -1);
            } else if (e.key === 'Enter' && this.activeIndex >= 0) {
                e.preventDefault();
                this.pick(this.suggestions[this.activeIndex]);
            } else if (e.key === 'Tab' && this.suggestions.length > 0) {
                e.preventDefault();
                this.pick(this.suggestions[this.activeIndex >= 0 ? this.activeIndex : 0]);
            } else if (e.key === 'Escape') {
                this.close();
            }
        },

        pick(cls) {
            const ta = this.$refs.input;
            const pos = ta.selectionStart;
            const val = ta.value;
            const before = val.slice(0, pos);
            const after = val.slice(pos);
            const wordStart = before.lastIndexOf(' ') + 1;
            const newVal = before.slice(0, wordStart) + cls + ' ' + after.trimStart();

            // Update textarea value and fire input so Livewire's wire:model picks it up
            ta.value = newVal;
            ta.dispatchEvent(new Event('input', { bubbles: true }));

            this.close();

            this.$nextTick(() => {
                const newPos = wordStart + cls.length + 1;
                ta.setSelectionRange(newPos, newPos);
                ta.focus();
            });
        },

        close() {
            this.open = false;
            this.activeIndex = -1;
        },

        delayClose() {
            setTimeout(() => this.close(), 150);
        },
    }));
}

if (window.Alpine) {
    registerTwAutocomplete();
} else {
    document.addEventListener('alpine:init', registerTwAutocomplete);
}

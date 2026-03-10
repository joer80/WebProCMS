import './tw-autocomplete.js';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';

document.addEventListener('alpine:init', () => {
    Alpine.data('richEditor', (initialContent, wireKey = 'content') => {
        // Stored outside Alpine's reactive scope to avoid Proxy wrapping,
        // which corrupts ProseMirror transactions.
        let _editor = null;

        return {
            active: {},
            headingLevel: '0',
            debounceTimer: null,
            sourceMode: false,
            sourceHtml: '',

            init() {
                _editor = new Editor({
                    element: this.$refs.editorEl,
                    extensions: [
                        StarterKit.configure({ link: false, underline: false }),
                        Underline,
                        Link.configure({ openOnClick: false }),
                        Image,
                    ],
                    content: initialContent || '',
                    editorProps: {
                        attributes: { class: 'rich-editor-content' },
                    },
                    onUpdate: ({ editor }) => {
                        this.syncState(editor);
                        clearTimeout(this.debounceTimer);
                        this.debounceTimer = setTimeout(() => {
                            const html = editor.getHTML();
                            this.$wire.set(wireKey, html === '<p></p>' ? '' : html);
                        }, 500);
                    },
                    onSelectionUpdate: ({ editor }) => this.syncState(editor),
                    onTransaction: ({ editor }) => this.syncState(editor),
                });
            },

            syncState(editor) {
                this.headingLevel = [1, 2, 3].find(l => editor.isActive('heading', { level: l }))?.toString() ?? '0';
                this.active = {
                    bold: editor.isActive('bold'),
                    italic: editor.isActive('italic'),
                    underline: editor.isActive('underline'),
                    strike: editor.isActive('strike'),
                    blockquote: editor.isActive('blockquote'),
                    codeBlock: editor.isActive('codeBlock'),
                    bulletList: editor.isActive('bulletList'),
                    orderedList: editor.isActive('orderedList'),
                    link: editor.isActive('link'),
                };
            },

            cmd() {
                return _editor.chain().focus();
            },

            setHeading(value) {
                const level = parseInt(value);
                if (level === 0) {
                    _editor.chain().focus().setParagraph().run();
                } else {
                    _editor.chain().focus().toggleHeading({ level }).run();
                }
            },

            toggleSource() {
                if (!this.sourceMode) {
                    this.sourceHtml = _editor ? _editor.getHTML() : '';
                    this.sourceMode = true;
                } else {
                    if (_editor) {
                        _editor.commands.setContent(this.sourceHtml, false);
                        const html = _editor.getHTML();
                        this.$wire.set(wireKey, html === '<p></p>' ? '' : html);
                    }
                    this.sourceMode = false;
                }
            },

            setLink() {
                const previous = _editor.getAttributes('link').href ?? '';
                const url = prompt('Enter URL:', previous);
                if (url === null) return;
                if (url === '') {
                    _editor.chain().focus().unsetLink().run();
                } else {
                    _editor.chain().focus().setLink({ href: url }).run();
                }
            },

            destroy() {
                _editor?.destroy();
                _editor = null;
            },
        };
    });
});

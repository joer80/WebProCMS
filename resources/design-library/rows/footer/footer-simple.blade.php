{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'bg-zinc-900 text-zinc-400 py-12 px-6', 'classes', 'section'); @endphp
<footer class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $brandClasses = content('__SLUG__', 'brand_classes', 'text-xl font-bold text-white', 'classes', 'content'); @endphp
        @php $taglineClasses = content('__SLUG__', 'tagline_classes', 'mt-2 text-sm', 'classes', 'content'); @endphp
        @php $navClasses = content('__SLUG__', 'nav_classes', 'flex flex-wrap gap-6 text-sm', 'classes', 'content'); @endphp
        @php $navLinkClasses = content('__SLUG__', 'nav_link_classes', 'hover:text-white transition-colors', 'classes', 'content'); @endphp
        @php $copyrightClasses = content('__SLUG__', 'copyright_classes', 'text-sm text-right', 'classes', 'content'); @endphp
        <div>
            <a href="/" class="{{ $brandClasses }}">{{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}</a>
            <p class="{{ $taglineClasses }}">{{ content('__SLUG__', 'tagline', 'Helping you build better things.', 'text', 'content') }}</p>
        </div>
        <nav class="{{ $navClasses }}">
            <a href="#" class="{{ $navLinkClasses }}">About</a>
            <a href="#" class="{{ $navLinkClasses }}">Blog</a>
            <a href="#" class="{{ $navLinkClasses }}">Contact</a>
            <a href="#" class="{{ $navLinkClasses }}">Privacy</a>
        </nav>
        <div class="{{ $copyrightClasses }}">
            <p>&copy; {{ date('Y') }} {{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}. All rights reserved.</p>
            <p>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></p>
        </div>
    </div>
</footer>

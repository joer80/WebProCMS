{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'bg-zinc-900 text-zinc-400 py-12 px-6'); @endphp
<footer class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $brandClasses = content('__SLUG__', 'brand_classes', 'text-xl font-bold text-white'); @endphp
        @php $taglineClasses = content('__SLUG__', 'tagline_classes', 'mt-2 text-sm'); @endphp
        @php $navClasses = content('__SLUG__', 'nav_classes', 'flex flex-wrap gap-6 text-sm'); @endphp
        @php $navLinkClasses = content('__SLUG__', 'nav_link_classes', 'hover:text-white transition-colors'); @endphp
        @php $copyrightClasses = content('__SLUG__', 'copyright_classes', 'text-sm text-right'); @endphp
        <div>
            <a href="/" class="{{ $brandClasses }}">{{ content('__SLUG__', 'brand_name', 'Brand') }}</a>
            <p class="{{ $taglineClasses }}">{{ content('__SLUG__', 'tagline', 'Helping you build better things.') }}</p>
        </div>
        <nav class="{{ $navClasses }}">
            <a href="#" class="{{ $navLinkClasses }}">About</a>
            <a href="#" class="{{ $navLinkClasses }}">Blog</a>
            <a href="#" class="{{ $navLinkClasses }}">Contact</a>
            <a href="#" class="{{ $navLinkClasses }}">Privacy</a>
        </nav>
        <div class="{{ $copyrightClasses }}">
            <p>&copy; {{ date('Y') }} {{ content('__SLUG__', 'brand_name', 'Brand') }}. All rights reserved.</p>
            <p>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></p>
        </div>
    </div>
</footer>

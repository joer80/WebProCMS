{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
<footer class="bg-zinc-900 text-zinc-400 py-12 px-6">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <a href="/" class="text-xl font-bold text-white">{{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}</a>
            <p class="mt-2 text-sm">{{ content('__SLUG__', 'tagline', 'Helping you build better things.', 'text', 'content') }}</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm">
            <a href="#" class="hover:text-white transition-colors">About</a>
            <a href="#" class="hover:text-white transition-colors">Blog</a>
            <a href="#" class="hover:text-white transition-colors">Contact</a>
            <a href="#" class="hover:text-white transition-colors">Privacy</a>
        </nav>
        <div class="text-sm text-right">
            <p>&copy; {{ date('Y') }} {{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}. All rights reserved.</p>
            <p>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></p>
        </div>
    </div>
</footer>

{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
<footer class="bg-zinc-900 text-zinc-400 py-12 px-6">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <a href="/" class="text-xl font-bold text-white">Brand</a>
            <p class="mt-2 text-sm">Helping you build better things.</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm">
            <a href="#" class="hover:text-white transition-colors">About</a>
            <a href="#" class="hover:text-white transition-colors">Blog</a>
            <a href="#" class="hover:text-white transition-colors">Contact</a>
            <a href="#" class="hover:text-white transition-colors">Privacy</a>
        </nav>
        <p class="text-sm">&copy; {{ date('Y') }} Brand. All rights reserved.</p>
    </div>
</footer>

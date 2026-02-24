{{--
@name Footer - Columns
@description Multi-column footer with logo, link groups, and newsletter signup.
@sort 20
--}}
<footer class="bg-zinc-900 pt-16 pb-8 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mb-12">
            <div class="col-span-2 md:col-span-1">
                <a href="/" class="text-xl font-bold text-white">Brand</a>
                <p class="mt-3 text-sm text-zinc-400 leading-relaxed">
                    A short description of your company and what makes you unique.
                </p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Company</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Product</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Docs</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Legal</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-zinc-800 pt-8 text-sm text-zinc-500 text-center">
            &copy; {{ date('Y') }} Brand, Inc. All rights reserved.
        </div>
    </div>
</footer>

{{--
@name 404 - Simple
@description Clean 404 not found page with large error code and navigation links.
@sort 10
--}}
<section class="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6">
    <div class="text-center">
        <div class="text-8xl font-black text-zinc-200 dark:text-zinc-700">404</div>
        <h1 class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">Page Not Found</h1>
        <p class="mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
            Sorry, we couldn't find the page you're looking for. It may have been moved or deleted.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="/" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Go Home
            </a>
            <a href="/contact" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                Contact Support
            </a>
        </div>
    </div>
</section>

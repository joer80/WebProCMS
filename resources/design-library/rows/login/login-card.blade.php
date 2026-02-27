{{--
@name Login - Card
@description Centered login card with email and password fields.
@sort 10
--}}
<section class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <a href="/" class="text-2xl font-bold text-zinc-900 dark:text-white">Brand</a>
            <h1 class="mt-4 text-xl font-semibold text-zinc-800 dark:text-zinc-100">Welcome back</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Sign in to your account</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-8">
            <form class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                    <input type="email" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                        <a href="#" class="text-xs text-primary hover:text-primary/80">Forgot password?</a>
                    </div>
                    <input type="password" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                    Sign In
                </button>
            </form>
        </div>
        <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
            Don't have an account? <a href="#" class="text-primary font-medium hover:text-primary/80">Sign up</a>
        </p>
    </div>
</section>

{{--
@name Contact - Form
@description Contact section with form fields and contact details sidebar.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Get in Touch</h2>
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-12">
            <form class="space-y-6">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">First Name</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Last Name</label>
                        <input type="text" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                    <input type="email" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Message</label>
                    <textarea rows="5" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"></textarea>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                    Send Message
                </button>
            </form>
            <div class="space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Contact Information</h3>
                    <div class="mt-4 space-y-4 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p>📍 123 Main Street, Suite 100<br>San Francisco, CA 94105</p>
                        <p>📞 (555) 123-4567</p>
                        <p>✉️ hello@example.com</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Business Hours</h3>
                    <div class="mt-4 space-y-1 text-zinc-500 dark:text-zinc-400 text-sm">
                        <p>Monday–Friday: 9am–6pm PST</p>
                        <p>Saturday: 10am–4pm PST</p>
                        <p>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

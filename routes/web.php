<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Spatie\ResponseCache\Middlewares\CacheResponse::class,
])->group(function (): void {
    Route::livewire('/', 'pages::home')->name('home');
    Route::livewire('about', 'pages::about')->name('about');
    Route::livewire('services', 'pages::services')->name('services');
    Route::livewire('services/content-editor', 'pages::services.content-editor')->name('services.content-editor');
    Route::livewire('locations', 'pages::locations')->name('locations');

    // SaaS pages
    Route::livewire('features', 'pages::features')->name('features');
    Route::livewire('pricing', 'pages::pricing')->name('pricing');

    // eCommerce pages
    Route::livewire('products', 'pages::products')->name('products');

    // Law pages
    Route::livewire('practice-areas', 'pages::practice-areas')->name('practice-areas');

    // Nonprofit pages
    Route::livewire('donate', 'pages::donate')->name('donate');
    Route::livewire('volunteer', 'pages::volunteer')->name('volunteer');

    // Healthcare pages
    Route::livewire('patients', 'pages::patients')->name('patients');
    Route::livewire('employers', 'pages::employers')->name('employers');
    Route::livewire('careers', 'pages::careers')->name('careers');

    // Volt version of blog - /resources/views/pages/blog/⚡index.blade.php
    Route::livewire('blog', 'pages::blog.index')->name('blog.index');
    Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');


    // new cached pages are inserted here
        Route::livewire('test', 'pages::test')->name('test');
    Route::livewire('contact', 'pages::contact')->name('contact');

    // LW4 version of blog (For comparison)- /app/Livewire/Blog2.php
    // Route::get('blog2', \App\Livewire\Blog2::class)->name('blog2.index');
});

// new uncached pages are inserted here

// Don't cache the contact page.
// Route::livewire('contact', 'pages::contact')->name('contact');

// Design Library live preview (temp files scoped per user+page, gitignored)
Route::get('design-editor-preview/{token}', function (string $token) {
    abort_if(auth()->guest(), 403);
    abort_if((int) explode('-', $token, 2)[0] !== auth()->id(), 403);

    $view = 'pages._editor-previews.'.$token;
    abort_if(! view()->exists($view), 404);

    $instance = app('livewire')->new('pages::_editor-previews.'.$token);

    return app()->call([$instance, '__invoke']);
})->middleware('web')->name('design-library.preview');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    Route::middleware('role:manager')->group(function (): void {
        Route::livewire('dashboard/blog', 'pages::dashboard.blog.index')->name('dashboard.blog.index');
        Route::livewire('dashboard/blog/create', 'pages::dashboard.blog.create')->name('dashboard.blog.create');
        Route::livewire('dashboard/blog/{post}/edit', 'pages::dashboard.blog.edit')->name('dashboard.blog.edit');

        Route::livewire('dashboard/categories', 'pages::dashboard.categories.index')->name('dashboard.categories.index');
        Route::livewire('dashboard/categories/create', 'pages::dashboard.categories.create')->name('dashboard.categories.create');
        Route::livewire('dashboard/categories/{category}/edit', 'pages::dashboard.categories.edit')->name('dashboard.categories.edit');

        Route::livewire('dashboard/shortcodes', 'pages::dashboard.shortcodes.index')->name('dashboard.shortcodes.index');
        Route::livewire('dashboard/shortcodes/create', 'pages::dashboard.shortcodes.create')->name('dashboard.shortcodes.create');
        Route::livewire('dashboard/shortcodes/{shortcode}/edit', 'pages::dashboard.shortcodes.edit')->name('dashboard.shortcodes.edit');

        Route::livewire('dashboard/locations', 'pages::dashboard.locations.index')->name('dashboard.locations.index');
        Route::livewire('dashboard/locations/create', 'pages::dashboard.locations.create')->name('dashboard.locations.create');
        Route::livewire('dashboard/locations/{location}/edit', 'pages::dashboard.locations.edit')->name('dashboard.locations.edit');

        Route::livewire('dashboard/tools', 'pages::dashboard.tools')->name('dashboard.tools');
        Route::livewire('dashboard/settings', 'pages::dashboard.settings')->name('dashboard.settings');

        Route::livewire('dashboard/users', 'pages::dashboard.users')->name('dashboard.users');

        Route::livewire('dashboard/pages', 'pages::dashboard.pages')->name('dashboard.pages');

        Route::livewire('dashboard/menus', 'pages::dashboard.menus')->name('dashboard.menus');

        Route::livewire('dashboard/design-library', 'pages::dashboard.design-library.index')->name('dashboard.design-library.index');
        Route::livewire('dashboard/pages/editor', 'pages::dashboard.pages.editor')->name('dashboard.design-library.editor');
    });
});

require __DIR__.'/settings.php';

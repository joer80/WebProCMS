<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Spatie\ResponseCache\Middlewares\CacheResponse::class,
])->group(function (): void {
    Route::get('/', App\Http\Controllers\HomeController::class)->name('home');
    Route::view('about', 'about')->name('about');
    Route::view('services', 'services')->name('services');
    Route::view('services/content-editor', 'services.content-editor')->name('services.content-editor');
    Route::livewire('locations', 'pages::locations')->name('locations');


    //Volt version of blog - /resources/views/pages/blog/⚡index.blade.php
    Route::livewire('blog', 'pages::blog.index')->name('blog.index');
    Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');

    //LW4 version of blog (For comparison)- /app/Livewire/Blog2.php
    //Route::get('blog2', \App\Livewire\Blog2::class)->name('blog2.index');
});

Route::livewire('contact', 'pages::contact')->name('contact');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

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
});

require __DIR__.'/settings.php';

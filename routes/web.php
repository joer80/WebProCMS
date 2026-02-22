<?php

use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Controllers\HomeController::class)->name('home');

Route::view('about', 'about')->name('about');

Route::view('services', 'services')->name('services');

Route::view('services/instant-query-editor', 'services.instant-query-editor')->name('services.instant-query-editor');

Route::livewire('contact', 'pages::contact')->name('contact');

Route::livewire('locations', 'pages::locations')->name('locations');

Route::livewire('blog', 'pages::blog.index')->name('blog.index');
Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard/blog', 'pages::dashboard.blog.index')->name('dashboard.blog.index');
    Route::livewire('dashboard/blog/create', 'pages::dashboard.blog.create')->name('dashboard.blog.create');
    Route::livewire('dashboard/blog/{post}/edit', 'pages::dashboard.blog.edit')->name('dashboard.blog.edit');

    Route::livewire('dashboard/categories', 'pages::dashboard.categories.index')->name('dashboard.categories.index');
    Route::livewire('dashboard/categories/create', 'pages::dashboard.categories.create')->name('dashboard.categories.create');
    Route::livewire('dashboard/categories/{category}/edit', 'pages::dashboard.categories.edit')->name('dashboard.categories.edit');

    Route::livewire('dashboard/shortcodes', 'pages::dashboard.shortcodes.index')->name('dashboard.shortcodes.index');
    Route::livewire('dashboard/shortcodes/create', 'pages::dashboard.shortcodes.create')->name('dashboard.shortcodes.create');
    Route::livewire('dashboard/shortcodes/{shortcode}/edit', 'pages::dashboard.shortcodes.edit')->name('dashboard.shortcodes.edit');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';

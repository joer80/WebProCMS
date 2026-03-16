<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Spatie\ResponseCache\Middlewares\CacheResponse::class,
])->group(function (): void {
    Route::livewire('/', 'pages::home')->name('home');

    // Volt version of blog - /resources/views/pages/blog/⚡index.blade.php
    Route::livewire('blog', 'pages::blog.index')->name('blog.index');
    Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');

    // LW4 version of blog (For comparison)- /app/Livewire/Blog2.php
    // Route::get('blog2', \App\Livewire\Blog2::class)->name('blog2.index');

    // new cached pages are inserted here
    Route::livewire('meeting-notes', 'pages::meeting-notes.index')->name('meeting-notes.index');
    Route::livewire('meeting-notes/{id}', 'pages::meeting-notes.show')->name('meeting-notes.show');
    Route::livewire('minutes', 'pages::minutes.index')->name('minutes.index');
    Route::livewire('minutes/{id}', 'pages::minutes.show')->name('minutes.show');
    Route::livewire('contact', 'pages::contact')->name('contact');
    Route::livewire('about', 'pages::about')->name('about');
    Route::livewire('404', 'pages::404')->name('404');
    Route::livewire('locations', 'pages::locations')->name('locations');
    Route::livewire('services', 'pages::services')->name('services');
});

// new uncached pages are inserted here

// Auth-required public routes — auth middleware always runs before cache to prevent bypass
Route::middleware(['auth'])->group(function (): void {
    Route::middleware([\Spatie\ResponseCache\Middlewares\CacheResponse::class])->group(function (): void {
        // new auth-cached pages are inserted here
    });

    // new auth-uncached pages are inserted here
});

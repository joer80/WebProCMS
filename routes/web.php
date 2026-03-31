<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Spatie\ResponseCache\Middlewares\CacheResponse::class,
])->group(function (): void {
    // new cached pages are inserted here
    Route::livewire('minutes', 'pages::minutes.index')->name('minutes.index');
    Route::livewire('minutes/{id}', 'pages::minutes.show')->name('minutes.show');
    Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');
    Route::livewire('blog', 'pages::blog.index')->name('blog.index');
    Route::livewire('events/{slug}', 'pages::events.show')->name('events.show');
    Route::livewire('events', 'pages::events.index')->name('events.index');
    Route::livewire('404', 'pages::404')->name('404');
    Route::livewire('locations', 'pages::locations')->name('locations');
    Route::livewire('services', 'pages::services')->name('services');
    Route::livewire('contact', 'pages::contact')->name('contact');
    Route::livewire('about', 'pages::about')->name('about');
    Route::livewire('/', 'pages::home')->name('home');
});

// new uncached pages are inserted here

// Auth-required public routes — auth middleware always runs before cache to prevent bypass
Route::middleware(['auth'])->group(function (): void {
    Route::middleware([\Spatie\ResponseCache\Middlewares\CacheResponse::class])->group(function (): void {
        // new auth-cached pages are inserted here
    });

    // new auth-uncached pages are inserted here
});

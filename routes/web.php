<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Spatie\ResponseCache\Middlewares\CacheResponse::class,
])->group(function (): void {
    // new cached pages are inserted here
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

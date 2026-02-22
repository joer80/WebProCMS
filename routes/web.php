<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('about', 'about')->name('about');

Route::view('services', 'services')->name('services');

Route::view('services/instant-query-editor', 'services.instant-query-editor')->name('services.instant-query-editor');

Route::livewire('contact', 'pages::contact')->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';

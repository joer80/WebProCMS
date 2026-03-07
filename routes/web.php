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
    Route::livewire('contact', 'pages::contact')->name('contact');
    Route::livewire('about', 'pages::about')->name('about');
    Route::livewire('404', 'pages::404')->name('404');
    Route::livewire('test', 'pages::test')->name('test');
});

// new uncached pages are inserted here

// Auth-required public routes — auth middleware always runs before cache to prevent bypass
Route::middleware(['auth'])->group(function (): void {
    Route::middleware([\Spatie\ResponseCache\Middlewares\CacheResponse::class])->group(function (): void {
        // new auth-cached pages are inserted here
    });

    // new auth-uncached pages are inserted here
});

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

        Route::livewire('dashboard/snippets', 'pages::dashboard.snippets.index')->name('dashboard.snippets.index');
        Route::livewire('dashboard/snippets/create', 'pages::dashboard.snippets.create')->name('dashboard.snippets.create');
        Route::livewire('dashboard/snippets/{snippet}/edit', 'pages::dashboard.snippets.edit')->name('dashboard.snippets.edit');

        Route::livewire('dashboard/locations', 'pages::dashboard.locations.index')->name('dashboard.locations.index');
        Route::livewire('dashboard/locations/create', 'pages::dashboard.locations.create')->name('dashboard.locations.create');
        Route::livewire('dashboard/locations/{location}/edit', 'pages::dashboard.locations.edit')->name('dashboard.locations.edit');

        Route::livewire('dashboard/forms', 'pages::dashboard.forms.index')->name('dashboard.forms.index');
        Route::livewire('dashboard/forms/create', 'pages::dashboard.forms.create')->name('dashboard.forms.create');
        Route::livewire('dashboard/forms/{form}/edit', 'pages::dashboard.forms.edit')->name('dashboard.forms.edit');
        Route::livewire('dashboard/forms/{form}/submissions', 'pages::dashboard.forms.submissions')->name('dashboard.forms.submissions');

        Route::livewire('dashboard/tools', 'pages::dashboard.tools')->name('dashboard.tools');
        Route::livewire('dashboard/templates', 'pages::dashboard.templates')->name('dashboard.templates');
        Route::redirect('dashboard/settings', '/dashboard/settings/general')->name('dashboard.settings');
        Route::livewire('dashboard/settings/general', 'pages::dashboard.settings.general')->name('dashboard.settings.general');
        Route::livewire('dashboard/settings/branding', 'pages::dashboard.settings.branding')->name('dashboard.settings.branding');
        Route::livewire('dashboard/settings/design', 'pages::dashboard.settings.design')->name('dashboard.settings.design');
        Route::livewire('dashboard/settings/advanced', 'pages::dashboard.settings.advanced')->name('dashboard.settings.advanced');

        Route::livewire('dashboard/users', 'pages::dashboard.users')->name('dashboard.users');

        Route::livewire('dashboard/pages', 'pages::dashboard.pages')->name('dashboard.pages');

        Route::livewire('dashboard/menus', 'pages::dashboard.menus')->name('dashboard.menus');

        Route::livewire('dashboard/design-library', 'pages::dashboard.design-library.index')->name('dashboard.design-library.index');
        Route::get('dashboard/design-library/preview/{type}/{id}', function (string $type, int $id) {
            abort_if(! in_array($type, ['row', 'page']), 404);

            if ($type === 'row') {
                $item = \App\Models\DesignRow::query()->findOrFail($id);
                $slug = 'preview-row-'.$id;
                $blade = str_replace('__SLUG__', $slug, $item->blade_code);
            } else {
                $item = \App\Models\DesignPage::query()->findOrFail($id);
                $rowNames = $item->row_names ?? [];
                $parts = [];

                foreach ($rowNames as $i => $templateName) {
                    $row = \App\Models\DesignRow::query()
                        ->where('source_file', 'like', '%/'.$templateName.'.blade.php')
                        ->first();

                    if (! $row) {
                        $parts[] = '<div style="padding:1rem 2rem;background:#fef9c3;color:#854d0e;font-family:monospace;font-size:0.75rem;">Row not found: '.$templateName.'</div>';

                        continue;
                    }

                    $slug = 'preview-page-'.$id.'-'.$i;
                    $blade = str_replace('__SLUG__', $slug, $row->blade_code);

                    try {
                        $parts[] = \Illuminate\Support\Facades\Blade::render($blade);
                    } catch (\Throwable $e) {
                        $parts[] = '<div style="padding:1rem 2rem;background:#fef3c7;color:#92400e;font-family:monospace;font-size:0.75rem;"><strong>'.$templateName.'</strong> — preview unavailable (requires live data)</div>';
                    }
                }

                return view('design-library-preview', ['content' => implode("\n", $parts), 'name' => $item->name]);
            }

            try {
                $content = \Illuminate\Support\Facades\Blade::render($blade);
            } catch (\Throwable $e) {
                $content = '<div style="padding:2rem;color:red;font-family:monospace;white-space:pre-wrap;">Preview error: '.htmlspecialchars($e->getMessage()).'</div>';
            }

            return view('design-library-preview', ['content' => $content, 'name' => $item->name]);
        })->name('dashboard.design-library.preview');
        Route::livewire('dashboard/redirects', 'pages::dashboard.redirects')->name('dashboard.redirects');
        Route::livewire('dashboard/pages/editor', 'pages::dashboard.pages.editor')->name('dashboard.design-library.editor');

        Route::livewire('dashboard/media-library', 'pages::dashboard.media-library.index')->name('dashboard.media-library.index');
    });
});

require __DIR__.'/settings.php';

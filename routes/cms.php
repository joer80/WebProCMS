<?php

use Illuminate\Support\Facades\Route;

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
        Route::livewire('dashboard/settings/api-keys', 'pages::dashboard.settings.api-keys')->name('dashboard.settings.api-keys');

        Route::livewire('dashboard/users', 'pages::dashboard.users')->name('dashboard.users');

        Route::livewire('dashboard/backups', 'pages::dashboard.backups')->name('dashboard.backups');
        Route::get('dashboard/backups/download/{filename}', function (string $filename) {
            // Prevent path traversal
            $filename = basename($filename);
            $path = storage_path('app/private/backups/'.$filename);

            abort_if(! file_exists($path), 404);

            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            abort_if($extension !== 'zip', 404);

            return response()->download($path, $filename);
        })->name('dashboard.backups.download');

        Route::livewire('dashboard/pages', 'pages::dashboard.pages')->name('dashboard.pages');

        Route::livewire('dashboard/menus', 'pages::dashboard.menus')->name('dashboard.menus');

        Route::livewire('dashboard/design-library', 'pages::dashboard.design-library.index')->name('dashboard.design-library.index');
        Route::get('dashboard/design-library/preview/{type}/{id}', function (string $type, int $id) {
            abort_if(! in_array($type, ['row', 'page']), 404);

            if ($type === 'row') {
                $item = \App\Models\DesignRow::query()->findOrFail($id);
                $slug = 'preview-row-'.$id;
                $blade = str_replace('__SLUG__', $slug, $item->bladeCodeFromFile());
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
                    $blade = str_replace('__SLUG__', $slug, $row->bladeCodeFromFile());

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

        // Content Types (Develop group)
        Route::livewire('dashboard/content-types', 'pages::dashboard.content-types.index')->name('dashboard.content-types.index');
        Route::livewire('dashboard/content-types/create', 'pages::dashboard.content-types.create')->name('dashboard.content-types.create');
        Route::livewire('dashboard/content-types/{contentTypeId}/edit', 'pages::dashboard.content-types.edit')->name('dashboard.content-types.edit');

        // Content Items (Content group — dynamic per type)
        Route::livewire('dashboard/content/{typeSlug}', 'pages::dashboard.content.index')->name('dashboard.content.index');
        Route::livewire('dashboard/content/{typeSlug}/create', 'pages::dashboard.content.create')->name('dashboard.content.create');
        Route::livewire('dashboard/content/{typeSlug}/{itemId}/edit', 'pages::dashboard.content.edit')->name('dashboard.content.edit');
    });
});

require __DIR__.'/settings.php';

// Language-prefixed public page routing (e.g. /es/about serves the same page as /about with Spanish content).
// This must be registered last so it only catches URLs not matched by other routes.
Route::get('/{lang}/{path?}', function (string $lang, string $path = '') {
    $activeLanguages = \App\Models\Setting::get('site.languages', [['code' => 'en']]);
    $langCodes = array_filter(array_column($activeLanguages, 'code'));

    if (! in_array($lang, $langCodes, true) || $lang === 'en') {
        abort(404);
    }

    // Store the active language so content() can resolve translated values.
    config(['cms.current_language' => $lang]);

    $pathToMatch = '/'.ltrim($path ?: '', '/');
    $router = app(\Illuminate\Routing\Router::class);
    $fakeRequest = \Illuminate\Http\Request::create(
        $pathToMatch,
        'GET',
        request()->query->all(),
        request()->cookies->all(),
        [],
        request()->server->all(),
    );
    $fakeRequest->server->set('REQUEST_URI', $pathToMatch);

    try {
        $matchedRoute = $router->getRoutes()->match($fakeRequest);
    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
        abort(404);
    }

    $matchedRoute->bind($fakeRequest);

    // LivewirePageController reads request()->route()->action['livewire_component'], so
    // we must point the real request's route resolver at the matched route before running it.
    $realRequest = request();
    $originalResolver = $realRequest->getRouteResolver();
    $realRequest->setRouteResolver(fn () => $matchedRoute);

    try {
        $result = $matchedRoute->run();
    } finally {
        $realRequest->setRouteResolver($originalResolver);
    }

    if ($result instanceof \Symfony\Component\HttpFoundation\Response) {
        return $result;
    }

    return response($result);
})->where(['lang' => '[a-z]{2,10}', 'path' => '.*'])->middleware('web');

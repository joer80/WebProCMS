<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureResponseCacheDriver();
    }

    /**
     * Apply the response cache driver saved in settings, falling back to file if unavailable.
     *
     * The result is cached in the file store to avoid a DB query on every request.
     * The cache is cleared when the setting is saved via the settings page.
     */
    protected function configureResponseCacheDriver(): void
    {
        try {
            if (Cache::store('file')->get('full_page_cache_driver') === 'redis') {
                config(['responsecache.cache_store' => 'redis']);
            }
        } catch (\Exception $e) {
            // Settings table may not exist yet (e.g. during initial migrations).
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}

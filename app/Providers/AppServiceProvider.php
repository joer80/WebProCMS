<?php

namespace App\Providers;

use App\Support\ContentCache;
use App\Support\SchemaCache;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
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
        $this->app->singleton(ContentCache::class);
        $this->app->singleton(SchemaCache::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives for the design library.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('dlItems', function (string $expression): string {
            $parts = array_map('trim', explode(',', $expression, 3));
            $slug = $parts[0];
            $prefixClean = trim($parts[1] ?? "''", " '\"");
            $var = trim($parts[2] ?? '$items');
            $gridKey = "'grid_{$prefixClean}'";

            return "<?php {$var} = json_decode(content({$slug}, {$gridKey}, ''), true) ?: []; ?>";
        });
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

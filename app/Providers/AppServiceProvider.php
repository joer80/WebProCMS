<?php

namespace App\Providers;

use App\Models\Setting;
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
        $this->configureMediaDisk();
        $this->registerBladeDirectives();
    }

    /**
     * Configure the `media` storage disk based on persisted settings.
     * Falls back to the local public disk if settings are unavailable or driver is local.
     */
    protected function configureMediaDisk(): void
    {
        try {
            $driver = Setting::get('storage.driver', 'local');

            if ($driver === 'local') {
                return;
            }

            $key = Setting::get('storage.key', '');
            $secret = Setting::get('storage.secret', '');
            $bucket = Setting::get('storage.bucket', '');
            $region = Setting::get('storage.region', 'us-east-1');
            $endpoint = Setting::get('storage.endpoint', '');
            $cdnUrl = Setting::get('storage.cdn_url', '');

            config(['filesystems.disks.media' => [
                'driver' => 's3',
                'key' => $key,
                'secret' => $secret,
                'region' => $region ?: 'us-east-1',
                'bucket' => $bucket,
                'url' => $cdnUrl ?: null,
                'endpoint' => $endpoint ?: null,
                'use_path_style_endpoint' => $driver === 'backblaze',
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ]]);
        } catch (\Throwable) {
            // DB may not be available during migrations — keep the local fallback
        }
    }

    /**
     * Register custom Blade directives for the design library.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('dlItems', function (string $expression): string {
            $parts = array_map('trim', explode(',', $expression, 4));
            $slug = $parts[0];
            $prefixClean = trim($parts[1] ?? "''", " '\"");
            $var = trim($parts[2] ?? '$items');
            $default = $parts[3] ?? "''";
            $gridKey = "'grid_{$prefixClean}'";

            return "<?php {$var} = json_decode(content({$slug}, {$gridKey}, {$default}), true) ?: []; \$__glc = config('cms.current_language', 'en'); if (\$__glc && \$__glc !== 'en') { {$var} = array_map(function(\$__gi) use (\$__glc) { foreach (array_keys(\$__gi) as \$__gk) { \$__lk = \$__gk . '__' . \$__glc; if (isset(\$__gi[\$__lk]) && \$__gi[\$__lk] !== '') { \$__gi[\$__gk] = \$__gi[\$__lk]; } } return \$__gi; }, {$var}); } ?>";
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

<?php

namespace Torrix\Presets;

use Laravel\Ui\Presets\Preset;
use RuntimeException;

class Uikit extends Preset
{
    private const VIEWS = [
        'welcome.blade.php',
        'demo.blade.php',
        'laravel.blade.php',
        'uikit.blade.php',
        'vuejs.blade.php',
        'layouts/app.blade.php',
    ];

    private const AUTH_VIEWS = [
        'auth/login.blade.php',
        'auth/passwords/email.blade.php',
        'auth/passwords/reset.blade.php',
        'auth/register.blade.php',
        'auth/verify.blade.php',
        'home.blade.php',
    ];

    public static function install(): void
    {
        static::ensureDirectoriesExist();
        static::updatePackages();
        static::updateSass();
        static::updateBootstrapping();
        static::removeNodeModules();
        static::exportViews(self::VIEWS);
        static::exportRoutes();
    }

    public static function auth(): void
    {
        static::exportViews(self::AUTH_VIEWS);
        static::exportControllers();
        static::exportAuthRoutes();
    }

    protected static function ensureDirectoriesExist(): void
    {
        if (! is_dir($directory = static::getViewPath('layouts'))) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        if (! is_dir($directory = static::getViewPath('auth/passwords'))) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }
    }

    protected static function updateSass(): void
    {
        copy(__DIR__ . '/../../resources/sass/app.scss', resource_path('sass/app.scss'));
    }

    protected static function updateBootstrapping(): void
    {
        copy(__DIR__ . '/../../resources/js/bootstrap.js', resource_path('js/bootstrap.js'));
    }

    protected static function updatePackageArray(array $packages): array
    {
        return ['uikit' => '^3.3.0'] + $packages;
    }

    protected static function exportViews($views): void
    {
        foreach ($views as $view) {
            copy(
                __DIR__ . '/../../resources/views/' . $view,
                static::getViewPath($view)
            );
        }
    }

    protected static function getViewPath(string $path): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'),
            $path,
        ]);
    }

    protected static function exportRoutes(): void
    {
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/../../resources/stubs/routes.stub'),
            FILE_APPEND
        );
    }

    protected static function exportAuthRoutes(): void
    {
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/../../resources/stubs/authroutes.stub'),
            FILE_APPEND
        );
    }

    protected static function exportControllers(): void
    {
        file_put_contents(
            app_path('Http/Controllers/HomeController.php'),
            file_get_contents(__DIR__.'/../../resources/stubs/controllers/HomeController.stub')
        );
    }
}

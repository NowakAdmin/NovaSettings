<?php

namespace Nowakadmin\NovaSettings;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Nowakadmin\NovaSettings\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'novasettings');

        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/nova-settings.php' => config_path('nova-settings.php'),
        ], 'nova-settings-config');

        // Publish translations
        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/novasettings'),
        ], 'nova-settings-lang');

        // Publish migration file
        $this->publishes([
            __DIR__.'/../database/migrations/create_settings_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_settings_table.php'),
        ], 'nova-settings-migration');

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register the tool's routes.
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', 'nova.auth', Authorize::class], 'novasettings')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', 'nova.auth', Authorize::class])
            ->prefix('nova-vendor/novasettings')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nova-settings.php', 'nova-settings');
    }
}

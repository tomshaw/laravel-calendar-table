<?php

/**
 * Class CalendarTableServiceProvider
 */

namespace TomShaw\CalendarTable\Providers;

use Illuminate\Support\ServiceProvider;
use TomShaw\CalendarTable\Commands\CalendarTableCommand;

/**
 * Service provider for the Calendar Table package.
 */
class CalendarTableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../resources/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../../config/config.php' => config_path('calendar-table.php')], 'config');
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'calendar-table');

        $this->commands([
            CalendarTableCommand::class,
        ]);
    }
}

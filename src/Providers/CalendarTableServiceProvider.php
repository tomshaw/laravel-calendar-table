<?php

namespace TomShaw\CalendarTable\Providers;

use Illuminate\Support\ServiceProvider;
use TomShaw\CalendarTable\Commands\CalendarTableCommand;

class CalendarTableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../resources/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../../config/config.php' => config_path('calendar-table.php')], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'calendar-table');

        $this->commands([
            CalendarTableCommand::class,
        ]);
    }
}

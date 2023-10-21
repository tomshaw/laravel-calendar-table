<?php

namespace TomShaw\CalendarTable\Providers;

use Illuminate\Support\ServiceProvider;
use TomShaw\CalendarTable\Commands\CalendarTableCommand;

class CalendarTableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../resources/database/migrations');
    }

    public function register()
    {
        $this->commands([
            CalendarTableCommand::class,
        ]);
    }
}

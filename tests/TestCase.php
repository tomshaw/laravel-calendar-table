<?php

namespace TomShaw\CalendarTable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\CalendarTable\Providers\CalendarTableServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            CalendarTableServiceProvider::class,
        ];
    }
}

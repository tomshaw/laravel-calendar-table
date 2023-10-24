<?php

namespace TomShaw\CalendarTable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\CalendarTable\Providers\CalendarTableServiceProvider;

class TestCase extends Orchestra
{
    protected $tableName = 'date_dimension';

    protected function getPackageProviders($app)
    {
        return [CalendarTableServiceProvider::class];
    }
}

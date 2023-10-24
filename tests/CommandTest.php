<?php

namespace TomShaw\CalendarTable\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CommandTest extends TestCase
{
    public function setup(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /** @test */
    public function it_runs_the_calendar_table_command()
    {
        // Arrange: Insert data into the database
        $exitCode = Artisan::call('calendar:table 2015');

        // Assert: Check if the command successfully run
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function it_checks_if_correct_number_of_records_exist_in_database()
    {
        // Arrange: Insert data into the database
        Artisan::call('calendar:table 2015');

        // Act: Retrieve the data from the database
        $result = DB::table($this->tableName)->count();

        // Assert: Check if the result is not null
        $this->assertEquals(3219, $result);
    }

    /** @test */
    public function it_checks_if_there_are_count_four_seasons_vivaldi()
    {
        // Arrange: Insert data into the database
        Artisan::call('calendar:table 2015');

        // Act: Retrieve the grouped quarters for the year from the database
        $result = DB::table($this->tableName)
            ->select('quarter', DB::raw('count(*) as total'))
            ->where('year', 2015)
            ->groupBy('quarter')
            ->get();

        // Assert: Check if the count of quarters is correct
        $this->assertCount(4, $result);
    }
}

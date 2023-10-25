<?php

namespace TomShaw\CalendarTable\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CommandTest extends TestCase
{
    protected ?string $tableName;

    protected ?string $consoleCommand;

    protected int $startYear = 2020;

    public function setup(): void
    {
        parent::setUp();

        $this->tableName = config('calendar-table.table_name');

        $this->consoleCommand = "calendar:table --year={$this->startYear}";

        Artisan::call('migrate');
    }

    /** @test */
    public function it_runs_the_calendar_table_command()
    {
        // Arrange: Insert data into the database
        $exitCode = Artisan::call($this->consoleCommand);

        // Assert: Check if the command successfully run
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function it_checks_if_result_count_is_correct_in_database()
    {
        // Arrange: Insert data into the database
        Artisan::call($this->consoleCommand);

        // Act: Retrieve the data from the database
        $result = DB::table($this->tableName)->count();

        // Assert: Check if the result count is correct
        $this->assertEquals(1394, $result);
    }

    /** @test */
    public function it_checks_if_result_count_year_has_four_quarters_in_database()
    {
        // Arrange: Insert data into the database
        Artisan::call($this->consoleCommand);

        // Act: Retrieve the grouped quarters for the year from the database
        $result = DB::table($this->tableName)
            ->select('quarter', DB::raw('count(*) as total'))
            ->where('year', $this->startYear)
            ->groupBy('quarter')
            ->get();

        // Assert: Check if the count of quarters is correct
        $this->assertCount(4, $result);
    }
}

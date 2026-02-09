<?php

namespace TomShaw\CalendarTable\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CommandTest extends TestCase
{
    protected ?string $tableName;

    protected ?string $consoleCommand;

    protected int $startYear;

    protected int $endYear;

    protected function setup(): void
    {
        parent::setUp();

        $this->tableName = config('calendar-table.table_name');

        // Get the current date
        $currentDate = Carbon::now();

        // Subtract 1 year from current date
        $this->startYear = (int) $currentDate->copy()->subYears(1)->toDateString();

        // Add 1 year to current date
        $this->endYear = (int) $currentDate->copy()->addYears(1)->toDateString();

        $this->consoleCommand = "calendar:table --start={$this->startYear} --end={$this->endYear}";

        Artisan::call('migrate');
    }

    public function it_runs_the_calendar_table_command()
    {
        // Arrange: Insert data into the database
        $exitCode = Artisan::call($this->consoleCommand);

        // Assert: Check if the command successfully run
        $this->assertEquals(0, $exitCode);
    }

    public function it_checks_if_database_is_correctly_filled()
    {
        // Arrange: Insert data into the database
        Artisan::call($this->consoleCommand);

        // Act: Retrieve the data from the database
        $result = DB::table($this->tableName)->count();

        // Assert: Check if the result count is correct
        $this->assertEquals(1096, $result);
    }

    public function test_it_asks_to_truncate_database_when_filled()
    {
        // Arrange: Insert data into the database
        Artisan::call($this->consoleCommand);

        // Arrange: Insert data into the database expect confirmation
        $this->artisan($this->consoleCommand)
            ->expectsQuestion('Table is currently filled would you like to truncate the table?', 'yes')
            ->assertExitCode(0);
    }
}

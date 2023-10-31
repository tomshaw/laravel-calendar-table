<?php

namespace TomShaw\CalendarTable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use USHolidays\Carbon;

class CalendarTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:table {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Laravel command to populate calendar table dates.';

    /**
     * The database table name.
     */
    protected string $tableName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tableName = config('calendar-table.table_name');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startYear = $this->option('start');
        $endYear = $this->option('end');

        if (! $startYear) {
            $startYear = $this->ask('Please enter a starting year');
        }

        if (! $endYear) {
            $endYear = Carbon::now()->year;
        }

        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        if (! $this->isValidYear($startYear)) {
            $this->error('Invalid start year format.');
        }

        if (! $this->isValidYear($endYear)) {
            $this->error('Invalid end year format.');
        }

        if ($startYear > $endYear) {
            $this->error('Starting period is greater than ending.');
        }

        if ($this->count()) {
            $this->error('Calendar table is not empty.');

            if ($this->confirm('Do you wish to truncate the table')) {
                $this->truncate();
            } else {
                return;
            }
        }

        $this->insert($startYear, $endYear);

        $count = $this->count();

        $this->info("Successfully added {$count} records starting from {$startYear} to {$endYear}.");
    }

    /**
     * Truncate the table.
     *
     * This method will truncate the table.
     *
     *
     * @throws \Illuminate\Database\QueryException If there is an error with the query.
     */
    public function truncate(): void
    {
        DB::table($this->tableName)->truncate();
    }

    /**
     * Count the number of records in the table.
     *
     * @return int The number of records in the table.
     */
    public function count()
    {
        return DB::table($this->tableName)->count();
    }

    /**
     * Insert dates from the start year to the current date into the table.
     *
     * @param  int  $startYear The start year for the date range.
     * @param  int  $endYear The start year for the date range.
     */
    public function insert(int $startYear, int $endYear)
    {
        $startDate = Carbon::createFromDate($startYear, 1, 1);
        $endDate = Carbon::createFromDate($endYear, 12, 31);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            DB::table($this->tableName)->insert([
                'date' => $date->toDateString(),
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year,
                'quarter' => $date->quarter,
                'day_of_week' => $date->dayOfWeek,
                'is_weekend' => $date->isWeekend(),
                'is_holiday' => $date->isHoliday(),
            ]);
        }
    }

    /**
     * Checks if the input is a valid year.
     *
     * This function checks if the input is a numeric value and if it falls within the range of 1000 to 9999,
     * which covers all valid 4-digit years. If both conditions are met, the function returns true;
     * otherwise, it returns false.
     *
     * @param  string  $input The input to be validated.
     * @return bool Returns true if the input is a valid year, false otherwise.
     */
    public function isValidYear($input)
    {
        // Check if the input is a numeric value
        if (is_numeric($input)) {
            // Convert the input to an integer
            $year = intval($input);

            // Check if the year is in the valid range
            if ($year >= 1000 && $year <= 9999) {
                return true;
            }
        }

        return false;
    }
}

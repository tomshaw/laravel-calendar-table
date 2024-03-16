<?php

namespace TomShaw\CalendarTable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use USHolidays\Carbon;

/**
 * Class CalendarTableCommand
 */
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
    protected $description = 'A Laravel command to sequence calendar table dates.';

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
    public function handle(): void
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
            $this->error('Starting year is greater than ending.');
        }

        if ($this->count()) {
            if ($this->confirm('Table is currently filled would you like to run the truncate command')) {
                $this->truncate();
            } else {
                return;
            }
        }

        $startTime = Carbon::now();

        $this->insert($startYear, $endYear);

        $count = $this->count();

        $endTime = Carbon::now();

        $executionTime = $endTime->diffInSeconds($startTime);

        $this->info("Added {$count} records starting from {$startYear} to {$endYear} taking {$executionTime} seconds.");
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
     * Insert dates from start year to end year into table.
     *
     * @param  int  $startYear  The start year for the date sequence.
     * @param  int  $endYear  The end year for the date sequence.
     */
    public function insert(int $startYear, int $endYear): void
    {
        $startDate = Carbon::createFromDate($startYear, 1, 1);
        $endDate = Carbon::createFromDate($endYear, 12, 31);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $season = $this->determineSeason($date);
            $fiscalYearQuarter = $this->fiscalYearQuarter($date);

            DB::table($this->tableName)->insert([
                'date' => $date->toDateString(),
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year,
                'quarter' => $date->quarter,
                'day_of_week' => $date->dayOfWeek,
                'is_weekend' => $date->isWeekend(),
                'is_holiday' => $date->isHoliday(),
                'day_of_year' => $date->dayOfYear,
                'week_of_year' => $date->weekOfYear,
                'is_leap_year' => $date->isLeapYear(),
                'season' => $season,
                'fiscal_year' => $fiscalYearQuarter['fiscal_year'],
                'fiscal_quarter' => $fiscalYearQuarter['fiscal_quarter'],
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
     * @param  int  $input  The input to be validated.
     * @return bool Returns true if the input is a valid year, false otherwise.
     */
    public function isValidYear(int $input)
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

    /**
     * Determines the season for a given date.
     *
     *
     * @throws \Exception If the 'seasons' configuration is not set.
     */
    public function determineSeason(Carbon $date): string
    {
        // Determine the season
        return collect(config('calendar-table.seasons'))->filter(function ($startMonth) use ($date) {
            return $date->month >= $startMonth;
        })->keys()->last() ?? 'Winter';
    }

    /**
     * Calculates the fiscal year and quarter for a given date.
     *
     *
     * @throws \Exception If the 'fiscal_year_start_month' configuration is not set.
     */
    public function fiscalYearQuarter(Carbon $date): array
    {
        $fiscalYear = $date->year;
        $fiscalQuarter = ceil($date->month / 3);

        $fiscalYearStartMonth = config('calendar-table.fiscal_year_start_month');
        if ($date->month >= $fiscalYearStartMonth) {
            $fiscalYear++;
            $fiscalQuarter = ceil(($date->month - $fiscalYearStartMonth + 1) / 3);
        } else {
            $fiscalQuarter = ceil(($date->month + 12 - $fiscalYearStartMonth + 1) / 3);
        }

        return [
            'fiscal_year' => $fiscalYear,
            'fiscal_quarter' => $fiscalQuarter,
        ];
    }
}

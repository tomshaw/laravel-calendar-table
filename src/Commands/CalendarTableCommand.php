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

        $this->tableName = config('calendar-table.table_name', 'date_dimension');
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
     * This function determines the season for a given date based on the start month of each season.
     * The function iterates through the seasons array and returns the first season where the start month is less than or equal to the current month.
     * If no season is found, the function returns 'Winter' as the default season.
     *
     * @param  Carbon  $date  The date for which the season is to be determined.
     * @return string The season for the given date.
     */
    public function determineSeason(Carbon $date): string
    {
        $seasons = config('calendar-table.seasons', [
            'Spring' => 3,
            'Summer' => 6,
            'Autumn' => 9,
            'Winter' => 12,
        ]);

        // Reverse the seasons array so we can find the first season where the start month is less than or equal to the current month
        $seasons = array_reverse($seasons, true);

        foreach ($seasons as $season => $startMonth) {
            if ($date->month >= $startMonth) {
                return $season;
            }
        }

        return 'Winter';
    }

    /**
     * Determines the fiscal year and quarter for a given date.
     *
     * This function determines the fiscal year and quarter for a given date based on the start month of the fiscal year.
     * The function calculates the fiscal year based on the month of the date and the start month of the fiscal year.
     * If the month of the date is greater than or equal to the start month of the fiscal year, the fiscal year is incremented by 1.
     * The fiscal quarter is calculated based on the month of the date and the start month of the fiscal year.
     *
     * @param  Carbon  $date  The date for which the fiscal year and quarter are to be determined.
     * @return array An array containing the fiscal year and quarter for the given date.
     */
    public function fiscalYearQuarter(Carbon $date): array
    {
        $fiscalYear = $date->year;
        $fiscalQuarter = ceil($date->month / 3);

        $fiscalYearStartMonth = config('calendar-table.fiscal_year_start_month', 10);
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

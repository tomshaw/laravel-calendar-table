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
     * The table name to be used.
     */
    protected string $tableName = 'date_dimension';

    /**
     * The fiscal year start month.
     */
    protected int $fiscalYearStartMonth = 10;

    /**
     * The seasons start month array.
     */
    protected array $seasons = [
        'Spring' => 3,
        'Summer' => 6,
        'Autumn' => 9,
        'Winter' => 12,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tableName = config('calendar-table.table_name', $this->tableName);
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
            $this->error("Invalid start year: {$startYear}");

            return;
        }

        if (! $this->isValidYear($endYear)) {
            $this->error("Invalid end year: {$endYear}");

            return;
        }

        if ($startYear > $endYear) {
            $this->error('Starting year is greater than ending.');

            return;
        }

        if ($this->count()) {
            if ($this->confirm('Table is currently filled would you like to truncate the table?')) {
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
    public function count(): int
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
     * Check if the year is valid.
     *
     * This function checks if the year is within the valid range.
     *
     * @param  int  $year  The year to be checked.
     * @return bool True if the year is valid, false otherwise.
     */
    public function isValidYear(int $year): bool
    {
        $startYear = config('calendar-table.date_range.start_year', Carbon::now()->subYears(20)->year);
        $endYear = config('calendar-table.date_range.end_year', Carbon::now()->addYears(20)->year);
        if ($year >= $startYear && $year <= $endYear) {
            return true;
        }

        return false;
    }

    /**
     * Determine the season for a given date.
     *
     * This function determines the season for a given date based on the start month of the season.
     *
     * @param  Carbon  $date  The date for which the season is to be determined.
     * @return string The season for the given date.
     */
    public function determineSeason(Carbon $date): string
    {
        $seasons = array_reverse(config('calendar-table.seasons', $this->seasons), true);

        foreach ($seasons as $season => $startMonth) {
            if ($date->month >= $startMonth) {
                return $season;
            }
        }

        return 'Winter';
    }

    /**
     * Determine the fiscal year and quarter for a given date.
     *
     * This function determines the fiscal year and quarter for a given date based on the start month of the fiscal year.
     *
     * @param  Carbon  $date  The date for which the fiscal year and quarter are to be determined.
     * @return array An array containing the fiscal year and quarter for the given date.
     */
    public function fiscalYearQuarter(Carbon $date): array
    {
        $fiscalYear = $date->year;
        $fiscalQuarter = ceil($date->month / 3);

        $fiscalYearStartMonth = config('calendar-table.fiscal_year_start_month', $this->fiscalYearStartMonth);
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

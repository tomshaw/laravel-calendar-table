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
    protected $signature = 'calendar:table {--year=}';

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
        $startYear = $this->option('year');

        if (! $startYear) {
            $startYear = $this->ask('Please enter a starting year');
        }

        $currentYear = Carbon::now()->year;

        if ($this->count()) {
            $this->error('Calendar table has already been filled.');

            if ($this->confirm('Do you wish to truncate the table')) {
                $this->truncate();
            } else {
                return;
            }
        }

        $this->insert($startYear);

        $count = $this->count();

        $this->info("Successfully added {$count} records starting from {$startYear} to {$currentYear}.");
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
     * @param  string  $startYear The start year for the date range.
     */
    public function insert(string $startYear)
    {
        $startDate = Carbon::createFromDate($startYear, 1, 1);
        $endDate = Carbon::now();

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
}

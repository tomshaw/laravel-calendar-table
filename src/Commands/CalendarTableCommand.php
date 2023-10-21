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
    protected $signature = 'calendar:table {year=2020}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Laravel command to populate calendar table dates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startYear = $this->argument('year');

        $this->populateCalendar($startYear);
    }

    public function populateCalendar(mixed $startYear)
    {
        $startDate = Carbon::createFromDate($startYear, 1, 1);
        $endDate = Carbon::now();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            DB::table('date_dimension')->insert([
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

<?php

return [
    /**
     * Defines the database table name.
     */
    'table_name' => 'date_dimension',

    /**
     * Defines the start month for each season.
     */
    'seasons' => [
        'Spring' => 3,
        'Summer' => 6,
        'Autumn' => 9,
        'Winter' => 12,
    ],

    /**
     * Defines the start month of the fiscal year.
     */
    'fiscal_year_start_month' => 10,

    /**
     * Defines the valid year range to populate the table.
     */
    'date_range' => [
        'start_year' => Carbon\Carbon::now()->subYears(20)->year,
        'end_year' => Carbon\Carbon::now()->addYears(20)->year,
    ],
];

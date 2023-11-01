<?php

/**
 * This configuration file defines the calendar table name, start months for each season and the start month of the fiscal year.
 *
 * @return array
 */
return [
    /**
     * User defined custom table name for database records.
     */
    'table_name' => 'date_dimension',

    /**
     * The 'seasons' array defines the start month for each season.
     * Each key is a season name, and each value is the start month for that season.
     */
    'seasons' => [
        'Spring' => 3,
        'Summer' => 6,
        'Autumn' => 9,
        'Winter' => 12,
    ],

    /**
     * The 'fiscal_year_start_month' defines the start month of the fiscal year.
     */
    'fiscal_year_start_month' => 10,
];

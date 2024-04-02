<?php

/**
 * Configuration file for the Laravel Calendar Table package.
 */
return [
    /**
     * User defined custom table name for database records.
     */
    'table_name' => 'date_dimension',

    /**
     * The 'seasons' array defines the start month for each season.
     *
     * This configuration assumes the meteorological seasons of the Northern Hemisphere, where the seasons are defined as follows:
     * - Spring starts in March (3rd month)
     * - Summer starts in June (6th month)
     * - Autumn starts in September (9th month)
     * - Winter starts in December (12th month)
     *
     * Users in the Southern Hemisphere should reconfigure the seasons approximately six months offset from those of the Northern Hemisphere:
     * - Spring starts in September
     * - Summer starts in December
     * - Autumn starts in March
     * - Winter starts in June
     */
    'seasons' => [
        'Spring' => 3,
        'Summer' => 6,
        'Autumn' => 9,
        'Winter' => 12,
    ],

    /**
     * The 'fiscal_year_start_month' option defines the start month of the fiscal year.
     *
     * Months are represented as numbers between 1 (January) and 12 (December).
     *
     * In this case, the fiscal year starts in October (10th month). This might be the case for a company or government that operates on a fiscal year that begins on October 1 and ends on September 30 of the following year.
     *
     * Users should adjust this setting to match their own fiscal year. For example, if the fiscal year starts in July, they would set this option to 7.
     */
    'fiscal_year_start_month' => 10,
];

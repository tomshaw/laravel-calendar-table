# Laravel Calendar Table 📈 📊

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/tomshaw/laravel-calendar-table/run-tests.yml?branch=master&style=flat-square&label=tests)
![issues](https://img.shields.io/github/issues/tomshaw/laravel-calendar-table?style=flat&logo=appveyor)
![forks](https://img.shields.io/github/forks/tomshaw/laravel-calendar-table?style=flat&logo=appveyor)
![stars](https://img.shields.io/github/stars/tomshaw/laravel-calendar-table?style=flat&logo=appveyor)
[![GitHub license](https://img.shields.io/github/license/tomshaw/laravel-calendar-table)](https://github.com/tomshaw/laravel-calendar-table/blob/master/LICENSE)

A **calendar table**, also known as a **date dimension table**, is a table in a database that is designed to help with date-related queries and reporting. It contains a row for each date within a certain range, often many years into the past and future. Each row contains various fields about the date, such as the day, month, year, quarter, day of the week, is it a weekend or a weekday, is it a holiday, etc.

The usefulness of a calendar table in database reporting comes from its ability to simplify and optimize date-related queries. Here are some of its benefits:

1. **Simplifying Queries**: Without a calendar table, extracting components of a date usually involves complex functions and calculations. With a calendar table, you can simply join your data with the calendar table to get these components.

2. **Improving Performance**: Date calculations can be CPU-intensive and slow down your queries. By moving these calculations into a calendar table, you can improve query performance.

3. **Consistency**: A calendar table ensures that date information is handled consistently across all queries and reports.

4. **Flexibility**: You can add custom fields to your calendar table to suit your business needs. For example, you could add fields for your company's fiscal periods or specific business events.

5. **Handling Missing Dates**: If your data has missing dates, those gaps can cause problems in reporting. A calendar table can help ensure continuity in your reports.

In summary, a calendar table is an extremely useful tool for anyone who frequently works with dates in their database.

## Installation
Require the package with composer using the following command:

```
composer require tomshaw/laravel-calendar-table
```

Publish the configuration file if you wish to change the default table name, season start months or fiscal_year_start_month.

```
php artisan vendor:publish --provider="TomShaw\CalendarTable\Providers\CalendarTableServiceProvider" --tag=config
```

## Database Migration

Run the database migration to create the calendar table.

```
php artisan migrate
```

## Console Command

The calendar table command accepts two optional parameters. If no **start** year is specified you will be prompted to enter one. If no **end** year is specified the current year will be used. 

> Note: If the table has been pre-filled you will be given the option to truncate.

```
php artisan calendar:table --start=2000 --end=2030
```

Sure, here's a README.md section that explains the configuration options for your Laravel Calendar Table package:

## Configuration

The Laravel Calendar Table package provides several configuration options that you can adjust to suit your needs. You can find these options in the `config.php` file.

### Table Name

The `table_name` option allows you to define a custom table name for the database records. By default, it is set to `'date_dimension'`.

```php
'table_name' => 'date_dimension',
```

### Seasons

The `seasons` array allows you to define the start month for each season. By default, it is configured for the meteorological seasons of the Northern Hemisphere:

- Spring starts in March
- Summer starts in June
- Autumn starts in September
- Winter starts in December

If you are in the Southern Hemisphere, you should reconfigure the seasons to start approximately six months later:

- Spring starts in September
- Summer starts in December
- Autumn starts in March
- Winter starts in June

```php
'seasons' => [
    'Spring' => 3,
    'Summer' => 6,
    'Autumn' => 9,
    'Winter' => 12,
],
```

### Fiscal Year Start Month

The `fiscal_year_start_month` option allows you to define the start month of the fiscal year. The value should be an integer between 1 (January) and 12 (December). By default, it is set to 10, meaning the fiscal year starts in October. Adjust this setting to match your own fiscal year.

```php
'fiscal_year_start_month' => 10,
```

### Date Range

The `date_range` array allows you to define the max start and end year range for populating the calendar table. 

- `start_year`: This option defines the earliest year for the calendar table. Defaults to 20 years before the current year. 

- `end_year`: This option defines the latest year for the calendar table. Defaults to 20 years after the current year.

```php
'date_range' => [
    'start_year' => Carbon\Carbon::now()->subYears(20)->year,
    'end_year' => Carbon\Carbon::now()->addYears(20)->year,
],
```

## Requirements

The package is compatible with Laravel 10 or later.

## Support

If you have any issues or questions please send a pull request.

## License

The MIT License (MIT). Please see [License](LICENSE) for more information.
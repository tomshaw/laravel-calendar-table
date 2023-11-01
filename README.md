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

In summary, a calendar table is an extremely useful tool for anyone who frequently works with dates in their database reporting. Happy Reporting. 😊

## Installation
Require the package with composer using the following command:

```
composer require tomshaw/laravel-calendar-table --dev
```

Publish configuration files if you wish to change the default table name **date_dimension**.

```
php artisan vendor:publish --provider="TomShaw\CalendarTable\Providers\CalendarTableServiceProvider" --tag=config
```

## Database Migration

Run the database migration to create the calendar table.

```
php artisan migrate
```

## Console Command

Run the console command to fill the calendar table.

> Note: The calendar table command accepts two optional parameters. If no **start** year is specified you will be prompted to enter one. If no **end** year is specified the current year will be used.

```
php artisan calendar:table --start=1990 --end=2030
```

## License

The MIT License (MIT). Please see [License](LICENSE) for more information.
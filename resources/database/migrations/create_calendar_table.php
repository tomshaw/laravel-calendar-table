<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('calendar-table.table_name');

        Schema::create($tableName, function (Blueprint $table) {
            $table->date('date')->primary();
            $table->integer('day');
            $table->integer('month');
            $table->integer('year');
            $table->integer('quarter');
            $table->integer('day_of_week');
            $table->boolean('is_weekend');
            $table->boolean('is_holiday');
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('calendar-table.table_name');

        Schema::dropIfExists($tableName);
    }
};

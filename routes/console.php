<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule live score updates every minute during match times
Schedule::command('football:sync --live')->everyMinute();

// Schedule today's matches sync every 30 minutes
Schedule::command('football:sync --today')->everyThirtyMinutes();

// Schedule full data sync daily at 6 AM
Schedule::command('football:sync')->dailyAt('06:00');

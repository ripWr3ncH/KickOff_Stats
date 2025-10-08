<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule live score updates every 2 minutes during match times
Schedule::command('matches:sync-today --live')->everyTwoMinutes();

// Schedule today's matches sync every 30 minutes to get new matches and finish old ones
Schedule::command('matches:sync-today')->everyThirtyMinutes();

// Clean up old live matches that are stuck every 5 minutes
Schedule::command('matches:finish-live')->everyFiveMinutes()->when(function () {
    // Only run if there are live matches older than 2 hours (matches should finish within 2 hours)
    return \App\Models\FootballMatch::where('status', 'live')
        ->where('updated_at', '<', now()->subHours(2))
        ->exists();
});

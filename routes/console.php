<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the RateHawk Hotel Dump Sync (Weekly)
// This will download the latest dump, add new hotels, update existing ones, 
// and remove hotels that are no longer in the catalog.
use Illuminate\Support\Facades\Schedule;

Schedule::command('ratehawk:fetch-dump --import --sync')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->onOneServer()
    ->runInBackground();

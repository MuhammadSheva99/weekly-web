<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminder:commitment')->weeklyOn(1, '08:00');
Schedule::command('reminder:progress')->weeklyOn(3, '08:00');
Schedule::command('reminder:review')->weeklyOn(5, '08:00');
Schedule::command('alert:belum-submit')->weeklyOn(1, '22:00');
Schedule::command('alert:underperform-tim')->weeklyOn(1, '09:00');
Schedule::command('alert:belum-submit-tim')->weeklyOn(2, '08:00');
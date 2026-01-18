<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan backup lama setiap hari jam 1 pagi
// Schedule::command('backup:clean')->daily()->at('01:00');

// Jalankan backup baru setiap hari jam 1:30 pagi
// Schedule::command('backup:run')->daily()->at('01:30');

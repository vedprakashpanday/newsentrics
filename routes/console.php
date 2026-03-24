<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\News;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Status 2 (Rejected) aur 15 din se purani news delete karein
    News::where('status', 2)
        ->where('created_at', '<', now()->subDays(15))
        ->delete();
})->daily();
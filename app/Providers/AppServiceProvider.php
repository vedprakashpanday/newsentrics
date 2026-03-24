<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // NAYA IMPORT
use App\Models\Category; // NAYA IMPORT



use App\Models\Setting; // Ise bhi top par add karein

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        // Yeh code layouts.frontend file ko automatically saari categories bhej dega
        View::composer('layouts.frontend', function ($view) {
            $view->with('categories', Category::all());
        });

        // Yeh code aapki setting ko frontend ke har ek blade file me pahuncha dega
    try {
        $site_settings = Setting::first();
        View::share('site_settings', $site_settings);
    } catch (\Exception $e) {
        // Migration na chalne par error se bachne ke liye
    }


    // Saare active ads ko har view ke sath share karein
$all_ads = \App\Models\Ad::where('is_active', true)->pluck('ad_code', 'slug');
View::share('all_ads', $all_ads);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // NAYA IMPORT
use App\Models\Category; // NAYA IMPORT

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
    }
}

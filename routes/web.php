<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\NewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/news/{slug}', [FrontendController::class, 'show'])->name('news.show');
Route::post('/news/{news_id}/comment', [\App\Http\Controllers\FrontendController::class, 'storeComment'])->name('comment.store');
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/search', [\App\Http\Controllers\FrontendController::class, 'search'])->name('news.search');
// Sidebar AI Widget ke liye route
Route::get('/api/sidebar-ai', [\App\Http\Controllers\FrontendController::class, 'sidebarAiWidget'])->name('api.sidebar.ai');


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Baaki news management routes yahan aayenge
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

   // Trending News Page
    Route::get('/admin/trending', function () {
        return view('admin.trending');
    })->name('admin.trending');

    
    // News Create Page
    Route::get('/admin/news/create', function () {
        return view('admin.create_news');
    })->name('news.create');

    // News Store Logic (Abhi sirf route banaya hai, controller baad mein likhenge)
  
    Route::get('/admin/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/admin/news/store', [NewsController::class, 'store'])->name('news.store');

    // YE NAYA ROUTE ADD KAREIN
    Route::post('/admin/generate-ai', [NewsController::class, 'generateAIContent'])->name('admin.generate.ai');
});
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');

require __DIR__.'/auth.php';

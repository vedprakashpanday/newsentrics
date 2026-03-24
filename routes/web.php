<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;

// --- FRONTEND ROUTES ---
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/news/{slug}', [FrontendController::class, 'show'])->name('news.show');
Route::post('/news/{news_id}/comment', [FrontendController::class, 'storeComment'])->name('comment.store');
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/search', [FrontendController::class, 'search'])->name('news.search');
Route::get('/api/sidebar-ai', [FrontendController::class, 'sidebarAiWidget'])->name('api.sidebar.ai');
Route::get('/contact-us', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact-us', [FrontendController::class, 'storeContact'])->name('contact.store');
Route::get('/p/{slug}', [PageController::class, 'showPage'])->name('page.show');

// --- AUTH & DEFAULT DASHBOARD ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

 Route::get('/home', function(){
    return view('welcome');
 })->name('admin.home');

// --- ADMIN ROUTES (Protected by 'auth' and 'admin' middleware) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Ab ye route automatic 'admin.dashboard' kehlayega
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // News Management (Ab har jagah se 'admin.' hata diya gaya hai)
    Route::get('/news/manage', [NewsController::class, 'index'])->name('news.manage');
    Route::get('/news', [NewsController::class, 'adminIndex'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news/store', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    Route::get('/news/{id}/ai-edit', [NewsController::class, 'aiEdit'])->name('news.ai_edit');
    Route::put('/news/{id}/ai-update', [NewsController::class, 'aiUpdate'])->name('news.ai_update'); 

    // Yahan se URL se extra '/admin' hata diya gaya hai
    Route::post('/news/{id}/approve', [NewsController::class, 'approve'])->name('news.approve');
    Route::post('/news/{id}/reject', [NewsController::class, 'reject'])->name('news.reject');
    
    Route::post('/generate-ai', [NewsController::class, 'generateAIContent'])->name('generate.ai');
   
Route::post('/generate-fetched-ai', [NewsController::class, 'expandFetchedNewsWithAi'])->name('generate_fetched.ai');
    Route::get('/trending', function () { return view('admin.trending'); })->name('trending');

    // Categories, Pages, Comments, Ads
    // Note: resource routes auto-name hote hain, 'admin.' unme lag jayega (e.g., admin.categories.index)
    Route::resource('categories', CategoryController::class);
    Route::resource('pages', PageController::class)->except(['show']);
    
    Route::get('/comments', [CommentController::class, 'adminIndex'])->name('comments.index');
    Route::patch('/comments/{id}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('/comments/{id}', [CommentController::class, 'adminDestroy'])->name('comments.destroy');
    
    Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
    Route::post('/ads/{id}', [AdController::class, 'update'])->name('ads.update');
    
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/messages', [AdminContactController::class, 'index'])->name('messages.index');
    Route::delete('/messages/{id}', [AdminContactController::class, 'destroy'])->name('messages.destroy');
});

Route::get('/api/sidebar-ai', [App\Http\Controllers\FrontendController::class, 'getSidebarAi'])->name('api.sidebar-ai');
require __DIR__.'/auth.php';
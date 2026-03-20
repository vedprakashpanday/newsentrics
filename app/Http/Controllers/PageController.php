<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // Admin: List all pages
    public function index() {
        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    // Admin: Edit Form
    public function edit(Page $page) {
        return view('admin.pages.edit', compact('page'));
    }

    // Admin: Update Logic
    public function update(Request $request, Page $page) {
        $request->validate(['title' => 'required', 'content' => 'required']);
        
        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()->route('pages.index')->with('success', 'Page updated!');
    }

    // Frontend: Show Page
  public function showPage($slug) {
    $page = Page::where('slug', $slug)->firstOrFail();
    return view('page', compact('page')); // 'frontend.page' view file honi chahiye
}
}

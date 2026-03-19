<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
   // Page Dikhane aur List karne ke liye
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    // Nayi Category Save karne ke liye
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) // Auto-generate slug (e.g. "Sports News" -> "sports-news")
        ]);

        return back()->with('success', 'Category added successfully!');
    }

    // Purani Category Update karne ke liye (Modal ke through)
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    // Category Delete karne ke liye
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Optional Check: Agar is category me news hain, toh delete na karne dein
        if ($category->news()->count() > 0) {
            return back()->with('error', 'Cannot delete! This category has active news articles.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }
}

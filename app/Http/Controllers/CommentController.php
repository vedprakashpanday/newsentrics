<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
   // CommentController.php mein ye methods update/add karein

public function adminIndex() {
    // Saare comments news ke sath fetch karo
    $comments = \App\Models\Comment::with('news')->latest()->get();
    return view('admin.comments.index', compact('comments'));
}

public function approve($id) {
    $comment = \App\Models\Comment::findOrFail($id);
    $comment->status = 1; // Approve kar diya
    $comment->save();

    return back()->with('success', 'Comment approved successfully!');
}

public function adminDestroy($id) {
    \App\Models\Comment::findOrFail($id)->delete();
    return back()->with('success', 'Comment deleted!');
}
}

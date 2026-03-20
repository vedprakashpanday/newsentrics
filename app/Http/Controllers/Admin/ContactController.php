<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Admin/ContactController.php
public function index() {
    $messages = \App\Models\Contact::latest()->get();
    return view('admin.contacts.index', compact('messages'));
}

public function destroy($id) {
    \App\Models\Contact::findOrFail($id)->delete();
    return back()->with('success', 'Message deleted!');
}
}

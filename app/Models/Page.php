<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    // In columns ko mass assignment ke liye allow karein
    protected $fillable = [
        'title', 
        'slug', 
        'content', 
        'meta_description'
    ];
}
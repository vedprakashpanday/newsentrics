<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //
   protected $fillable = [
        'category_id', 'title', 'slug', 'country', 'image', 'content', 'keywords'
    ];

    // YE NAYA FUNCTION ADD KAREIN
    // Yeh Laravel ko batayega ki ek News ke multiple comments ho sakte hain
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    // Ek news kisi ek category ki hoti hai
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['news_id', 'name', 'email', 'comment'];

    // Ek comment kis news ka hai, wo batane ke liye
    public function news()
    {
        return $this->belongsTo(News::class);
    }
}

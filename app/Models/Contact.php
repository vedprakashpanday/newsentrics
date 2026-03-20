<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // Ye line add karna zaroori hai
    protected $fillable = ['name', 'email', 'message', 'is_read'];
}
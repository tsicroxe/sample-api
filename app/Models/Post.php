<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasApiTokens;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'excerpt', 'content'];
}

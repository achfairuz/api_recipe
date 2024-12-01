<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    protected $fillable = [
        'recipe_id',
        'user_id',
        'comment',
    ];


    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function recipe_id()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }
}

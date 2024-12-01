<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;



    public $timestamps = false;
    protected $table = 'recipes';
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'money',
        'cooking_time',
        'servings',
        'image',
        'code_yt',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category_id()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}

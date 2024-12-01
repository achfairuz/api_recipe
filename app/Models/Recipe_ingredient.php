<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe_ingredient extends Model
{
    use HasFactory;


    public $timestamps = false;
    protected $table = 'recipe_ingredients';
    protected $fillable = [
        'recipe_id',
        'ingredient_name',
        'quantity',


    ];

    public function recipe_id()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }
}

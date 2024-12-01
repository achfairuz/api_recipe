<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe_step extends Model
{
    use HasFactory;


    public $timestamps = false;
    protected $table = 'recipe_steps';
    protected $fillable = [
        'recipe_id',
        'step_number',
        'step',


    ];

    public function recipe_id()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }
}

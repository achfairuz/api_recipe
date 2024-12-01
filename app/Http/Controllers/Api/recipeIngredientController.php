<?php

namespace App\Http\Controllers\Api;

use App\Models\Recipe;
use App\Models\Recipe_ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class recipeIngredientController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'recipe_id' => 'required|integer|exists:recipes,id', // Pastikan recipe_id valid
                'ingredient_name' => 'required|string',
                'quantity' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation error', $validator->errors());
            }

            // Cari recipe berdasarkan ID
            $recipe = Recipe::find($request->recipe_id);

            if (!$recipe) {
                return $this->sendError('404', 'Recipe Not Found');
            }

            // Siapkan input untuk disimpan
            $input = [
                'recipe_id' => $recipe->id,
                'ingredient_name' => $request->ingredient_name,
                'quantity' => $request->quantity,
            ];

            // Simpan data ingredient
            $ingredient = Recipe_ingredient::create($input);

            // Respon sukses
            return $this->sendResponse($ingredient, 'Ingredient created successfully');
        } catch (\Throwable $th) {
            Log::error('Error creating ingredient: ' . $th->getMessage());
            return $this->sendError('Error Created', $th->getMessage());
        }
    }

    public function storeLoop(Request $request)
    {
        try {
            // Validasi input array
            $validator = Validator::make($request->all(), [
                'ingredients' => 'required|array',
                'ingredients.*.recipe_id' => 'required|integer',
                'ingredients.*.ingredient_name' => 'required|string',
                'ingredients.*.quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation error', $validator->errors());
            }

            // Siapkan data untuk insert
            $ingredients = array_map(function ($ingredient) {
                return [
                    'recipe_id' => $ingredient['recipe_id'],
                    'ingredient_name' => $ingredient['ingredient_name'],
                    'quantity' => $ingredient['quantity'],

                ];
            }, $request->ingredients);

            // Insert banyak data sekaligus
            Recipe_ingredient::insert($ingredients);

            return $this->sendResponse($ingredients, 'Ingredients created successfully');
        } catch (\Throwable $th) {
            Log::error('Error creating ingredients: ' . $th->getMessage());
            return $this->sendError('Error Created', $th->getMessage());
        }
    }

    public function updateMany(Request $request)
    {
        try {
            // Validasi input array
            $validator = Validator::make($request->all(), [
                'ingredients' => 'required|array',
                'ingredients.*.id' => 'required|integer|exists:recipe_ingredients,id',
                'ingredients.*.ingredient_name' => 'sometimes|string',
                'ingredients.*.quantity' => 'sometimes|integer',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Proses update satu per satu
            foreach ($request->ingredients as $ingredientData) {
                $ingredient = Recipe_ingredient::find($ingredientData['id']);
                if ($ingredient) {
                    $ingredient->update(array_filter($ingredientData, function ($key) {
                        return in_array($key, ['ingredient_name', 'quantity']);
                    }, ARRAY_FILTER_USE_KEY));
                }
            }

            return $this->sendResponse($request->ingredients, 'Ingredients updated successfully');
        } catch (\Throwable $th) {
            Log::error('Error updating ingredients: ' . $th->getMessage());
            return $this->sendError('Error Updating', $th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    public function deleteMany(String $recipe_id)
    {
        try {



            Recipe_ingredient::where('recipe_id', $recipe_id)->delete();

            return $this->sendResponse('success', 'Ingredients deleted successfully');
        } catch (\Throwable $th) {
            Log::error('Error deleting ingredients: ' . $th->getMessage());
            return $this->sendError('Error Deleting', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showIngredientsByRecipeId($recipe_id)
    {
        try {

            $ingredients = Recipe_ingredient::where('recipe_id', $recipe_id)->get();


            if ($ingredients->isEmpty()) {
                return $this->sendError('No ingredients found for this recipe.');
            }

            // Return response sukses dengan data ingredients
            return $this->sendResponse($ingredients, 'Ingredients retrieved successfully');
        } catch (\Throwable $th) {
            // Menangani error jika ada masalah
            Log::error('Error fetching ingredients: ' . $th->getMessage());
            return $this->sendError('Error Fetching Ingredients', $th->getMessage());
        }
    }
}

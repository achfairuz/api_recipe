<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\Recipe_ingredient;
use App\Models\Recipe_step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Storage;


class recipeController extends BaseController
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
                'category_id' => 'required',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'cooking_time' => 'required|integer',
                'money' => 'required',
                'servings' => 'required|integer',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
                'code_yt' => 'nullable|string|max:255',

            ]);

            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors());
            }

            $category = Category::find($request->category_id);

            if ($category == null) {
                return $this->sendError('404', 'ID Category Not Found');
            }

            $user = Auth::user()->id;

            if ($user === null) {
                return $this->sendError('Error Authentication', 'The user is not logged in yet');
            }


            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }

            $input = [

                'user_id' => $user,
                'image' => $imagePath,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'money' => $request->money,
                'description' => $request->description,
                'cooking_time' => $request->cooking_time,
                'servings' => $request->servings,
                'code_yt' => $request->code_yt,



            ];

            $recipe = Recipe::create($input);

            return $this->sendResponse($recipe, 'Data Created Successfull');
        } catch (\Throwable $th) {
            Log::error('Store Error: ' . $th->getMessage());
            return $this->sendError('An error occurred while saving the recipe', ['error' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showById(String $id)
    {

        $recipe = Recipe::with('user')->find($id);

        // Mengecek apakah data ditemukan
        if (!$recipe) {
            return $this->sendError('404', 'ID Not Found');
        }


        $recipe->money = number_format($recipe->money, 0, ',', '.'); // Format: 100.000
        $recipe->image = asset('storage/' . $recipe->image);

        return $this->sendResponse($recipe, 'Show Success');
    }



    public function showAll()
    {
        $recipe = Recipe::with('user')->get();

        return $this->sendResponse($recipe, 'Recipe Show succesfull');
    }

    public function showByUser()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->sendError('Error Authentication', 'The user is not logged in yet');
        }

        $recipes = Recipe::where('user_id', $user->id)->with('user')->get();

        if ($recipes->isEmpty()) {
            return $this->sendResponse([], "Please add the recipe first");
        }


        $recipes->each(function ($recipe) {
            $recipe->money = number_format($recipe->money, 0, ',', '.');
            $recipe->image = asset('storage/' . $recipe->image);
        });

        return $this->sendResponse($recipes, 'Recipe show by user successful');
    }

    public function showByCategory(string $id)
    {

        if (empty($id)) {
            return $this->sendError('400', 'Category ID is required');
        }

        $recipes = Recipe::where('category_id', $id)
            ->with('user') // Relasi user jika ada
            ->get();


        if ($recipes->isEmpty()) {
            return $this->sendError('404', 'ID Category Not Found');
        }

        $recipes->each(function ($recipe) {
            $recipe->money = number_format($recipe->money, 0, ',', '.');
            $recipe->image = asset('storage/' . $recipe->image);
        });

        return $this->sendResponse($recipes, 'Show by category successful');
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

    //  ERROR IMAGE
    public function update(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'sometimes|integer',
                'cooking_time' => 'sometimes|integer',
                'money' => 'sometimes',
                'servings' => 'sometimes|integer',
                'code_yt' => 'sometimes|string',
            ]);


            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $recipe = Recipe::find($request->id);

            if (!$recipe) {
                return $this->sendError('Recipe not found', []);
            }

            $user = Auth::user();

            if (!$user) {
                return $this->sendError('404', 'The user is not logged in yet');
            }

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                // if ($recipe->image && Storage::disk('public')->exists($recipe->image)) {
                //     Storage::disk('public')->delete($recipe->image);
                // }

                // Simpan gambar baru
                $imagePath = $request->file('image')->store('images', 'public');
                // $request['image'] = $request->file('image')->hashName();
            }

            $recipe->update([
                'name' => $request['name'] ?? $recipe->name,
                'image' => $imagePath ?? $recipe->image,
                'category_id' => $request['category_id'] ?? $recipe->category_id,
                'cooking_time' => $request['cooking_time'] ?? $recipe->cooking_time,
                'money' => $request['money'] ?? $recipe->money,
                'servings' => $request['servings'] ?? $recipe->servings,
                'code_yt' => $request['code_yt'] ?? $recipe->code_yt,
            ]);

            // if ($request->hasFile('image')) {
            //     // Hapus gambar lama jika ada
            //     if ($recipe->image && Storage::disk('public')->exists($recipe->image)) {
            //         Storage::disk('public')->delete($recipe->image);
            //     }

            //     // Simpan gambar baru
            //     $imagePath = $request->files->$request->file('image')->storeAs('images', 'public');
            //     $request['image'] = $imagePath;
            // }



            // $recipe->update($request->all());

            return $this->sendResponse($recipe, 'Update Successfull');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return $this->sendError('404', 'ID Not Found');
        }

        $recipe->delete();

        return $this->sendResponse('success', 'Recipe delete successfull');
    }

    public function storeFullRecipe(Request $request)
    {
        DB::beginTransaction(); // Untuk memastikan transaksi berjalan atomik
        try {
            // Decode JSON jika data dikirim sebagai string
            $ingredients = is_string($request->ingredients)
                ? json_decode($request->ingredients, true)
                : $request->ingredients;

            $steps = is_string($request->steps)
                ? json_decode($request->steps, true)
                : $request->steps;

            // Validasi apakah data berhasil di-decode
            if (!is_array($ingredients) || !is_array($steps)) {
                return $this->sendError('Validation Error', [
                    'ingredients' => ['The ingredients field must be an array.'],
                    'steps' => ['The steps field must be an array.'],
                ]);
            }

            // Validasi data resep
            $validator = Validator::make(array_merge($request->all(), [
                'ingredients' => $ingredients,
                'steps' => $steps,
            ]), [
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'cooking_time' => 'required|integer',
                'money' => 'nullable|integer',
                'servings' => 'required|integer',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'code_yt' => 'nullable|string|max:255',
                'ingredients' => 'required|array',
                'ingredients.*.ingredient_name' => 'required|string',
                'ingredients.*.quantity' => 'required|string',
                'steps' => 'required|array',
                'steps.*.step_number' => 'required|integer',
                'steps.*.step' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Upload gambar
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('images', 'public')
                : null;

            // Simpan data resep
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'cooking_time' => $request->cooking_time,
                'money' => $request->money,
                'servings' => $request->servings,
                'image' => $imagePath,
                'code_yt' => $request->code_yt,
            ]);

            // Simpan bahan-bahan
            $ingredientsData = array_map(function ($ingredient) use ($recipe) {
                return [
                    'recipe_id' => $recipe->id,
                    'ingredient_name' => $ingredient['ingredient_name'],
                    'quantity' => $ingredient['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $ingredients);
            Recipe_ingredient::insert($ingredientsData);

            // Simpan langkah-langkah
            $stepsData = array_map(function ($step) use ($recipe) {
                return [
                    'recipe_id' => $recipe->id,
                    'step_number' => $step['step_number'],
                    'step' => $step['step'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $steps);
            Recipe_step::insert($stepsData);

            DB::commit();
            return $this->sendResponse($recipe, 'Recipe, ingredients, and steps created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error creating recipe: ' . $th->getMessage());
            return $this->sendError('Error occurred', $th->getMessage());
        }
    }
}

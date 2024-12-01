<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Recipe_step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class recipeStepController extends BaseController
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
                'recipe_id' => 'required',
                'step_number' => 'required|integer',
                'step' => 'required|string',

            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $recipe_id = Recipe::find($request->recipe_id);

            if (!$recipe_id) {
                return $this->sendError('404', 'Recipe Not Found');
            }

            $input = [
                'recipe_id' => $request->recipe_id,
                'step_number' => $request->recipe_number,
                'step' => $request->step,
            ];
            $step = Recipe_step::create($input);

            return $this->sendResponse($step, 'Step created succesfull');
        } catch (\Throwable $th) {
            return $this->sendError('Error Created', $th->getMessage());
        }
    }
    public function Created(Request $request)
    {
        try {
            // Validasi input array
            $validator = Validator::make($request->all(), [
                'steps' => 'required|array',
                'steps.*.recipe_id' => 'required|integer|exists:recipes,id',
                'steps.*.step_number' => 'required|integer',
                'steps.*.step' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Siapkan data untuk insert
            $steps = array_map(function ($step) {
                return [
                    'recipe_id' => $step['recipe_id'],
                    'step_number' => $step['step_number'],
                    'step' => $step['step'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $request->steps);

            // Insert banyak data sekaligus
            Recipe_step::insert($steps);

            return $this->sendResponse($steps, 'Steps created successfully');
        } catch (\Throwable $th) {
            Log::error('Error creating steps: ' . $th->getMessage());
            return $this->sendError('Error Created', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function showStepByRecipeId(String $recipe_id)
    {
        $step = Recipe_step::where('recipe_id', $recipe_id)->get();

        if (!$step) {
            return $this->sendError('404', 'Step Not Found');
        }

        return $this->sendResponse($step, 'Success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'step_number' => 'sometimes|integer',
                'step' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Temukan langkah berdasarkan ID
            $step = Recipe_step::find($id);

            if (!$step) {
                return $this->sendError('404', 'Step Not Found');
            }

            // Perbarui data langkah
            $step->update($request->only(['step_number', 'step']));

            return $this->sendResponse($step, 'Step updated successfully');
        } catch (\Throwable $th) {
            Log::error('Error updating step: ' . $th->getMessage());
            return $this->sendError('Error Updating', $th->getMessage());
        }
    }
    public function updateMany(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'steps' => 'required|array',
                'steps.*.id' => 'required|integer|exists:recipe_steps,id',
                'steps.*.step_number' => 'sometimes|integer',
                'steps.*.step' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Perbarui data langkah satu per satu
            foreach ($request->steps as $stepData) {
                $step = Recipe_step::find($stepData['id']);

                if ($step) {
                    $step->update(array_filter($stepData, function ($key) {
                        return in_array($key, ['step_number', 'step']);
                    }, ARRAY_FILTER_USE_KEY));
                }
            }

            return $this->sendResponse($request->steps, 'Steps updated successfully');
        } catch (\Throwable $th) {
            Log::error('Error updating steps: ' . $th->getMessage());
            return $this->sendError('Error Updating', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Temukan langkah berdasarkan ID
            $step = Recipe_step::find($id);

            if (!$step) {
                return $this->sendError('404', 'Step Not Found');
            }

            // Hapus langkah
            $step->delete();

            return $this->sendResponse(null, 'Step deleted successfully');
        } catch (\Throwable $th) {
            Log::error('Error deleting step: ' . $th->getMessage());
            return $this->sendError('Error Deleting', $th->getMessage());
        }
    }

    public function destroyRecipe(string $id)
    {
        try {
            // Temukan langkah berdasarkan ID
            $step = Recipe_step::where('recipe_id',  $id);

            if (!$step) {
                return $this->sendError('404', 'Step Not Found');
            }

            // Hapus langkah
            $step->delete();

            return $this->sendResponse(null, 'Step deleted successfully');
        } catch (\Throwable $th) {
            Log::error('Error deleting step: ' . $th->getMessage());
            return $this->sendError('Error Deleting', $th->getMessage());
        }
    }
}

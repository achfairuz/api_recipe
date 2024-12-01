<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\commentController;
use App\Http\Controllers\Api\recipeController;
use App\Http\Controllers\Api\recipeIngredientController;
use App\Http\Controllers\Api\recipeStepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/category/create', [categoryController::class, 'store']);
    Route::get('/category/show', [categoryController::class, 'showCategory']);

    Route::put('/category/update/{id}', [categoryController::class, 'update']);
    Route::delete('/category/delete/{id}', [categoryController::class, 'destroy']);

    Route::post('/recipe/create', [recipeController::class, 'store']);
    Route::get('/recipe/showByID/{id}', [recipeController::class, 'showById']);
    Route::get('/recipe/show', [recipeController::class, 'showAll']);
    Route::get('/recipe/showByUser', [recipeController::class, 'showByUser']);
    Route::get('/recipe/showByCategory/{id}', [recipeController::class, 'showByCategory']);
    Route::get('/recipe/showByUser', [recipeController::class, 'showByUser']);
    Route::put('/recipe/update/{id}', [recipeController::class, 'update']);
    Route::delete('/recipe/delete/{id}', [recipeController::class, 'destroy']);

    Route::post('/recipeIngredients/create', [recipeIngredientController::class, 'store']);
    Route::post('/recipeIngredients/createLoop', [recipeIngredientController::class, 'storeLoop']); //input banyak
    Route::put('/recipeIngredients/updateMany', [recipeIngredientController::class, 'updateMany']); //update banyak
    Route::delete('/recipeIngredients/delete/{recipe_id}', [recipeIngredientController::class, 'deleteMany']); //delete banyak
    Route::get('/recipeIngredients/show/{recipe_id}', [recipeIngredientController::class, 'showIngredientsByRecipeId']); //delete banyak


    Route::get('/recipestep/show/{recipe_id}', [recipeStepController::class, 'showStepByRecipeId']); //delete banyak

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile/update', [AuthController::class, 'update']);
    Route::post('/recipe/post', [recipeController::class, 'storeFullRecipe']);


    Route::post('/comment', [commentController::class, 'store']);
    Route::get('/comment/show/{recipe_id}', [commentController::class, 'showByRecipe']);
});

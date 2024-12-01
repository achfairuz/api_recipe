<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class commentController extends BaseController
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
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required',
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error Validation: ', $validator->errors());
        }

        $user = Auth::user()->id;
        if ($user == null) {
            return $this->sendError('404', 'User_id Not Found');
        }

        $recipe_id = Recipe::find($request->recipe_id);
        if ($recipe_id == null) {
            return $this->sendError('404', 'User_id Not Found');
        }

        $input = $request->all();
        $input['user_id'] = $user;
        $comment = Comment::create($input);

        return $this->sendResponse($comment, 'Comment Succesfull');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function showByRecipe(string $id)
    {
        $comments = Comment::with('user')->where('recipe_id', $id)->get();

        if ($comments->isEmpty()) {
            return $this->sendError('404', 'ID Recipe Not Found');
        }



        $comments->each(function ($comment) {
            // Periksa apakah path gambar sudah ada dan bukan URL lengkap
            $comment->user->image = $comment->user->image
                ? (filter_var($comment->user->image, FILTER_VALIDATE_URL)
                    ? $comment->user->image  // Jika sudah URL, biarkan
                    : asset('storage/' . $comment->user->image) // Jika path relatif, tambahkan asset('storage/')
                )
                : null; // Jika tidak ada gambar, set null
        });



        return $this->sendResponse($comments, 'Show Comment Successfull');
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
}

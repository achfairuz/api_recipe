<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class categoryController extends BaseController
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
                'name' => 'required|string|max:255',
                'icon' => 'required|string|max:255',


            ]);

            if ($validator->fails()) {

                return $this->sendError('Validation error', $validator->errors());
            };


            $data = $request->all();
            $catgeory = Category::create($data); // Sesuaikan dengan model yang Anda gunakan

            // Kembalikan respons sukses
            return $this->sendResponse($catgeory, 'created successfully');
        } catch (\Exception $e) {
            // Tangani kesalahan dengan log dan respons
            Log::error('Store error: ' . $e->getMessage());

            return $this->sendError('error message', $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function showCategory()
    {
        $category = Category::all();

        return $this->sendResponse($category, 'category show successfull');
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
    public function update(Request $request)
    {
        try {

            $category = Category::find($request->id);
            if ($category === null) {
                return $this->sendError('404', 'ID Not Found');
            }

            $category->update($request->all());

            return $this->sendResponse('success', 'Update Successfull');
        } catch (\Throwable $th) {
            Log::error('error' . $th->getMessage());
            return $this->sendError('error', $th->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        $category->delete();

        return $this->sendResponse('success', 'delete successfull');
    }
}

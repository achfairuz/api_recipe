<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Storage;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required|min:8'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            };

            if (!Auth::attempt($request->only('username', 'password'))) {
                return $this->sendError('Login failed', ['error' => 'Invalid email or password']);
            }

            $user = Auth::user();



            $success = $user;
            $success['token'] = $user->createToken('authToken')->plainTextToken;
            return $this->sendResponse(

                $success,
                'Login Successfully',
            );
        } catch (\Throwable $th) {
            Log::error('message' . $th->getMessage());
            return $this->sendError('error' . $th->getMessage());
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|unique:users,username',
                'alamat' => 'required',
                'password' => 'required|min:8'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation error', $validator->errors());
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);

            $user = User::create($input);

            return $this->sendResponse($user, 'User registered successfully');
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error("message" . $e->getMessage());

            // Return an error response
            return $this->sendError('Registration failed', ['error' => $e->getMessage()]);
        }
    }

    public function profile()
    {
        $id_user = Auth::user()->id;


        if ($id_user == null) {
            return $this->sendError('404', 'Login terlebih dahulu');
        }

        $user = User::find($id_user);
        if (!$user) {
            return $this->sendError('404', 'Pengguna tidak ditemukan');
        }

        $imageUrl = $user->image ? asset('storage/' . $user->image) : null;
        $userData = $user->toArray();
        $userData['image'] = $imageUrl;

        return $this->sendResponse($userData, 'Tampilkan profil berhasil');
    }


    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if ($user == null) {
            return $this->sendError('404', 'ID Not Found');
        }

        // Validasi request
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'username' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Jika ada file image yang diunggah
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('profile_images', 'public'); // Simpan di storage/app/public/profile_images

            // Hapus gambar lama jika ada
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Simpan path baru ke database
            $validatedData['image'] = $path;
        }

        // Update data user
        $user->update($validatedData);

        // Kembalikan respons sukses dengan data user
        $user->image = $user->image ? asset('storage/' . $user->image) : null; // Tambahkan URL lengkap ke gambar
        return $this->sendResponse($user, 'Data update successful');
    }
}

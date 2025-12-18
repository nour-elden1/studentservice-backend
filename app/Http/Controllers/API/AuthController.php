<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->successResponse('Registered successfully', [
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        // نجيب اليوزر
        $user = User::where('email', $data['email'])->first();

        // لو مفيش يوزر أو الباسورد غلط
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials', [
                'email' => ['The provided credentials are incorrect.'],
            ], 401);
        }

        // نحذف كل التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        // نعمل توكن جديد
        $token = $user->createToken('api_token')->plainTextToken;

        return $this->successResponse('Logged in successfully', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse('Logged out successfully');
    }

    public function me(Request $request)
    {
        return $this->successResponse('Authenticated user', $request->user());
    }
}

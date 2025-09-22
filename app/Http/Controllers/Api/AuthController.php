<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Hash;
use App\Services\JwtService;

class AuthController extends Controller
{
    use ApiResponses;

    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $this->jwtService->generateToken(['user_id' => $user->id]);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $this->jwtService->generateToken(['user_id' => $user->id]);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');
    }
}

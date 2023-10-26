<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse {
        $validated = $request->validated();

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password']
        ];

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request): JsonResponse {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }


        $token = auth()->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(): JsonResponse {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token): JsonResponse {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Patient;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Public Patient Registration
     * Creates a new User strictly with the 'patient' role and an associated Patient profile.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            // 1. Create user account strictly as patient
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Password casting hashes this automatically
                'role' => 'patient', // Force role to patient
                'phone' => $request->phone,
            ]);

            // 2. Create associated patient profile
            Patient::create([
                'user_id' => $user->id,
            ]);

            // 3. Generate Sanctum authentication token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse('Patient registered successfully', [
                'user' => $user->load('patient'),
                'token' => $token,
            ], 201);
        });
    }

    /**
     * User Login
     * Authenticates user, verifies password, and issues a Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid email or password', null, 401);
        }

        // Load specific profile based on role
        if ($user->isDoctor()) {
            $user->load('doctor.specialty');
        } elseif ($user->isPatient()) {
            $user->load('patient');
        }

        // Issue Sanctum Bearer token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('Login successful', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * User Logout
     * Revokes the current token used for authentication.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logged out successfully', null);
    }

    /**
     * Get Current Authenticated User & Profile
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isDoctor()) {
            $user->load('doctor.specialty');
        } elseif ($user->isPatient()) {
            $user->load('patient');
        }

        return $this->successResponse('User profile retrieved successfully', [
            'user' => $user,
        ]);
    }
}

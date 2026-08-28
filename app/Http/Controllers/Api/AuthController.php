<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login with mobile number and PIN.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'mobile_number' => [
                'required',
                'string',
                'max:20',
            ],

            'pin' => [
                'required',
                'string',
            ],
        ]);

        $user = User::where(
            'mobile_number',
            $credentials['mobile_number']
        )->first();

        if (
            !$user ||
            !Hash::check(
                $credentials['pin'],
                $user->password
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The mobile number or PIN is incorrect.',
            ], 401);
        }

        $token = $user->createToken(
            'dorofarm-android'
        )->plainTextToken;

        $user->load('farm');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
            ],

            'farm' => $user->farm ? [
                'id' => $user->farm->id,
                'farm_name' => $user->farm->farm_name,
                'registration_date' => $user->farm->registration_date,
            ] : null,
        ]);
    }

    /**
     * Logout from the Android application.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Return the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->load('farm');

        return response()->json([
            'success' => true,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
            ],

            'farm' => $user->farm ? [
                'id' => $user->farm->id,
                'farm_name' => $user->farm->farm_name,
                'registration_date' => $user->farm->registration_date,
            ] : null,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:20',
                'unique:users,mobile_number,' . $request->user()->id,
            ],
        ]);

        $user = $request->user();

        $user->update([
            'name' => $validated['name'],
            'mobile_number' => $validated['mobile_number'],
        ]);

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
            ],
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
            ],

            'new_password' => [
                'required',
                'string',
                'min:4',
                'confirmed',
            ],
        ]);

        $user = $request->user();

        // Verify the current password.
        if (!Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'The current password is incorrect.',
            ], 422);
        }

        // Make sure the new password is different.
        if (Hash::check(
            $validated['new_password'],
            $user->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'The new password must be different from the current password.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make(
                $validated['new_password']
            ),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}

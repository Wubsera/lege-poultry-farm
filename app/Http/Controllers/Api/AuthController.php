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
     *
     * Accepted formats:
     * 09xxxxxxxx
     * 9xxxxxxxx
     * 2519xxxxxxxx
     * +2519xxxxxxxx
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

        $mobileNumber = $this->normalizeMobileNumber(
            $credentials['mobile_number']
        );

        if ($mobileNumber === null) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid Ethiopian mobile number.',
            ], 422);
        }

        $user = User::where(
            'mobile_number',
            $mobileNumber
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
     * Normalize Ethiopian mobile numbers to 09xxxxxxxx.
     */
    private function normalizeMobileNumber(string $mobileNumber): ?string
    {
        // Remove spaces, hyphens, and other formatting characters.
        $mobileNumber = preg_replace('/[\s\-().]/', '', $mobileNumber);

        // Remove leading + from international format.
        if (str_starts_with($mobileNumber, '+')) {
            $mobileNumber = substr($mobileNumber, 1);
        }

        // 2519xxxxxxxx -> 09xxxxxxxx
        if (preg_match('/^2519\d{8}$/', $mobileNumber)) {
            return '0' . substr($mobileNumber, 3);
        }

        // 9xxxxxxxx -> 09xxxxxxxx
        if (preg_match('/^9\d{8}$/', $mobileNumber)) {
            return '0' . $mobileNumber;
        }

        // 09xxxxxxxx -> 09xxxxxxxx
        if (preg_match('/^09\d{8}$/', $mobileNumber)) {
            return $mobileNumber;
        }

        return null;
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

        $mobileNumber = $this->normalizeMobileNumber(
            $validated['mobile_number']
        );

        if ($mobileNumber === null) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid Ethiopian mobile number.',
            ], 422);
        }

        $user = $request->user();

        $existingUser = User::where(
            'mobile_number',
            $mobileNumber
        )
            ->where('id', '!=', $user->id)
            ->exists();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        $user->update([
            'name' => $validated['name'],
            'mobile_number' => $mobileNumber,
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

        if (!Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            return response()->json([
                'success' => false,
                'message' => 'The current password is incorrect.',
            ], 422);
        }

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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FarmUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (!$admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only the farm administrator can manage users.',
            ], 403);
        }

        $users = User::where('farm_id', $admin->farm_id)
            ->select([
                'id',
                'name',
                'email',
                'mobile_number',
                'farm_id',
                'is_admin',
                'is_active',
                'created_at',
            ])
            ->orderBy('is_admin', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (!$admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only the farm administrator can create users.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'pin' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:4',
                'max:20',
                'confirmed',
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

        $existingUser = User::where(
            'mobile_number',
            $mobileNumber
        )->exists();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'mobile_number' => $mobileNumber,
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['pin']),

            // Always use the authenticated admin's farm.
            'farm_id' => $admin->farm_id,

            // Users created here are never administrators.
            'is_admin' => false,

            // New staff users are active by default.
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
                'is_admin' => $user->is_admin,
                'is_active' => $user->is_active,
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();

        if (!$admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only the farm administrator can manage users.',
            ], 403);
        }

        // Important: only users belonging to the admin's farm.
        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in your farm.',
            ], 404);
        }

        // Admin account cannot be modified here.
        if ($user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'The farm administrator account cannot be modified here.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'pin' => [
                'nullable',
                'string',
                'regex:/^[0-9]+$/',
                'min:4',
                'max:20',
                'confirmed',
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

        $updateData = [
            'name' => $validated['name'],
            'mobile_number' => $mobileNumber,
            'email' => $validated['email'] ?? null,
        ];

        if (!empty($validated['pin'])) {
            $updateData['password'] = Hash::make($validated['pin']);
        }

        // Never update farm_id, is_admin, or is_active here.
        $user->update($updateData);
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
                'is_admin' => $user->is_admin,
                'is_active' => $user->is_active,
            ],
        ]);
    }

    /**
     * Activate or deactivate a staff user.
     */
    public function toggleStatus(
        Request $request,
        int $id
    ): JsonResponse {
        $admin = $request->user();

        if (!$admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only the farm administrator can manage users.',
            ], 403);
        }

        // Important: only users belonging to the admin's farm.
        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in your farm.',
            ], 404);
        }

        // The farm administrator can never be deactivated.
        if ($user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'The farm administrator account cannot be deactivated.',
            ], 403);
        }

      $user->update([
    'is_active' => !$user->is_active,
]);

// Immediately revoke all existing API sessions when deactivated.
if (!$user->is_active) {
    $user->tokens()->delete();
}

$user->refresh();

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => $user->is_active
                ? 'User activated successfully.'
                : 'User deactivated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_number' => $user->mobile_number,
                'farm_id' => $user->farm_id,
                'is_admin' => $user->is_admin,
                'is_active' => $user->is_active,
            ],
        ]);
    }

    /**
     * Normalize Ethiopian mobile numbers to 09xxxxxxxx.
     */
    private function normalizeMobileNumber(
        string $mobileNumber
    ): ?string {
        $mobileNumber = preg_replace(
            '/[\s\-().]/',
            '',
            $mobileNumber
        );

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
}

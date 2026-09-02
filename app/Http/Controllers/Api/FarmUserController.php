<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FarmUserController extends Controller
{
    /**
     * List users belonging to the authenticated user's farm.
     */
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

    /**
     * Create a new user under the authenticated admin's farm.
     */
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
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

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

            // New users are not administrators.
            'is_admin' => false,
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
            ],
        ], 201);
    }

    /**
     * Update a user belonging to the authenticated admin's farm.
     */
    public function update(
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

        /*
        |--------------------------------------------------------------------------
        | Find User Only Inside Admin's Farm
        |--------------------------------------------------------------------------
        */

        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in your farm.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Do Not Allow Editing the Farm Admin
        |--------------------------------------------------------------------------
        */

        if ($user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'The farm administrator account cannot be modified here.',
            ], 403);
        }

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
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

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

        // Only change PIN when a new PIN was supplied.
        if (!empty($validated['pin'])) {
            $updateData['password'] = Hash::make(
                $validated['pin']
            );
        }

        // Never update farm_id or is_admin here.
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

        // 2519xxxxxxxx
        if (preg_match('/^2519\d{8}$/', $mobileNumber)) {
            return '0' . substr($mobileNumber, 3);
        }

        // 9xxxxxxxx
        if (preg_match('/^9\d{8}$/', $mobileNumber)) {
            return '0' . $mobileNumber;
        }

        // 09xxxxxxxx
        if (preg_match('/^09\d{8}$/', $mobileNumber)) {
            return $mobileNumber;
        }

        return null;
    }
}

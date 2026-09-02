<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FarmUserController extends Controller
{
    /**
     * Display users belonging to the authenticated admin's farm.
     */
    public function index()
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can manage users.');
        }

        $users = User::where('farm_id', $admin->farm_id)
            ->orderBy('is_admin', 'desc')
            ->orderBy('name')
            ->get();

        return view('farm-settings.users.index', [
            'users' => $users,
            'farm' => $admin->farm,
        ]);
    }

    /**
     * Show the create user form.
     */
    public function create()
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can create users.');
        }

        return view('farm-settings.users.create', [
            'farm' => $admin->farm,
        ]);
    }

    /**
     * Store a new user under the authenticated admin's farm.
     */
    public function store(Request $request)
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can create users.');
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
            return back()
                ->withInput()
                ->withErrors([
                    'mobile_number' =>
                        'Please enter a valid Ethiopian mobile number.',
                ]);
        }

        $existingUser = User::where(
            'mobile_number',
            $mobileNumber
        )->exists();

        if ($existingUser) {
            return back()
                ->withInput()
                ->withErrors([
                    'mobile_number' =>
                        'This mobile number is already registered.',
                ]);
        }

        User::create([
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

        return redirect()
            ->route('farm-settings.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the edit user form.
     */
    public function edit(int $id)
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can manage users.');
        }

        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            abort(404, 'User not found in your farm.');
        }

        // The farm administrator cannot be edited here.
        if ($user->is_admin) {
            abort(
                403,
                'The farm administrator account cannot be modified here.'
            );
        }

        return view('farm-settings.users.edit', [
            'user' => $user,
            'farm' => $admin->farm,
        ]);
    }

    /**
     * Update a user belonging to the authenticated admin's farm.
     */
    public function update(Request $request, int $id)
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can manage users.');
        }

        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            abort(404, 'User not found in your farm.');
        }

        // The farm administrator cannot be edited here.
        if ($user->is_admin) {
            abort(
                403,
                'The farm administrator account cannot be modified here.'
            );
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
            return back()
                ->withInput()
                ->withErrors([
                    'mobile_number' =>
                        'Please enter a valid Ethiopian mobile number.',
                ]);
        }

        $existingUser = User::where(
            'mobile_number',
            $mobileNumber
        )
            ->where('id', '!=', $user->id)
            ->exists();

        if ($existingUser) {
            return back()
                ->withInput()
                ->withErrors([
                    'mobile_number' =>
                        'This mobile number is already registered.',
                ]);
        }

        $updateData = [
            'name' => $validated['name'],
            'mobile_number' => $mobileNumber,
            'email' => $validated['email'] ?? null,
        ];

        // Only update PIN when a new PIN was provided.
        if (!empty($validated['pin'])) {
            $updateData['password'] = Hash::make(
                $validated['pin']
            );
        }

        // Never change farm_id or is_admin.
        $user->update($updateData);

        return redirect()
            ->route('farm-settings.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Activate or deactivate a staff user.
     */
    public function toggleStatus(int $id)
    {
        $admin = auth()->user();

        if (!$admin->is_admin) {
            abort(403, 'Only the farm administrator can manage users.');
        }

        // Important: only find users belonging to the admin's farm.
        $user = User::where('id', $id)
            ->where('farm_id', $admin->farm_id)
            ->first();

        if (!$user) {
            abort(404, 'User not found in your farm.');
        }

        // The farm administrator can never be deactivated here.
        if ($user->is_admin) {
            abort(
                403,
                'The farm administrator account cannot be deactivated.'
            );
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return redirect()
            ->route('farm-settings.users.index')
            ->with(
                'success',
                $user->is_active
                    ? 'User activated successfully.'
                    : 'User deactivated successfully.'
            );
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

        // 09xxxxxxxx -> unchanged
        if (preg_match('/^09\d{8}$/', $mobileNumber)) {
            return $mobileNumber;
        }

        return null;
    }
}

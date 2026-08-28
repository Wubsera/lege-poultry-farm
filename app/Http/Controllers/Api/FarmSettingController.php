<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FarmSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FarmSettingController extends Controller
{
    /**
     * Return farm information for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $farm = $user->farm;

        $settings = FarmSetting::where(
            'farm_id',
            $user->farm_id
        )->first();

        return response()->json([
            'success' => true,

            'data' => [
                'farm' => $farm ? [
                    'id' => $farm->id,
                    'farm_name' => $farm->farm_name,
                    'registration_date' => $farm->registration_date
                        ? Carbon::parse(
                            $farm->registration_date
                        )->format('Y-m-d')
                        : null,
                ] : null,

                'settings' => $settings ? [
                    'registered_birds' => (int) $settings->registered_birds,
                    'farm_name' => $settings->farm_name,
                    'registration_date' => $settings->registration_date
                        ? Carbon::parse(
                            $settings->registration_date
                        )->format('Y-m-d')
                        : null,
                ] : null,
            ],
        ]);
    }

    /**
     * Update farm information.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'farm_name' => [
                'required',
                'string',
                'max:255',
            ],

            'registered_birds' => [
                'required',
                'integer',
                'min:1',
            ],

            'registration_date' => [
                'required',
                'date',
            ],
        ]);

        $farm = $request->user()->farm;

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found.',
            ], 404);
        }

        DB::transaction(function () use (
            $farm,
            $validated
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update Farm
            |--------------------------------------------------------------------------
            */

            $farm->update([
                'farm_name' => $validated['farm_name'],
                'registration_date' => $validated['registration_date'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Farm Settings
            |--------------------------------------------------------------------------
            */

            FarmSetting::updateOrCreate(
                [
                    'farm_id' => $farm->id,
                ],
                [
                    'registered_birds' =>
                        $validated['registered_birds'],

                    'farm_name' =>
                        $validated['farm_name'],

                    'registration_date' =>
                        $validated['registration_date'],
                ]
            );
        });

        $farm->refresh();

        $settings = FarmSetting::where(
            'farm_id',
            $farm->id
        )->first();

        return response()->json([
            'success' => true,

            'message' =>
                'Farm information saved successfully.',

            'data' => [
                'farm' => [
                    'id' => $farm->id,

                    'farm_name' =>
                        $farm->farm_name,

                    'registration_date' =>
                        $farm->registration_date
                            ? Carbon::parse(
                                $farm->registration_date
                            )->format('Y-m-d')
                            : null,
                ],

                'settings' => $settings ? [
                    'registered_birds' =>
                        (int) $settings->registered_birds,

                    'farm_name' =>
                        $settings->farm_name,

                    'registration_date' =>
                        $settings->registration_date
                            ? Carbon::parse(
                                $settings->registration_date
                            )->format('Y-m-d')
                            : null,
                ] : null,
            ],
        ]);
    }
}

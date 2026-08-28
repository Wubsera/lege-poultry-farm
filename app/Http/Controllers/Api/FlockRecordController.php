<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FarmSetting;
use App\Models\FlockRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlockRecordController extends Controller
{
    /**
     * Return current flock status and flock records
     * for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        $farmId = $request->user()->farm_id;

        /*
        |--------------------------------------------------------------------------
        | Farm Information
        |--------------------------------------------------------------------------
        */

        $farm = FarmSetting::where(
            'farm_id',
            $farmId
        )->first();

        $totalBirds = $farm
            ? (int) $farm->registered_birds
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Latest Flock Record
        |--------------------------------------------------------------------------
        */

        $latestRecord = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Current Sick Birds
        |--------------------------------------------------------------------------
        */

        $sickBirds = $latestRecord
            ? (int) $latestRecord->sick
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Total Recovered Birds
        |--------------------------------------------------------------------------
        */

        $recoveredBirds = (int) FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('recovered');

        /*
        |--------------------------------------------------------------------------
        | Total Deaths
        |--------------------------------------------------------------------------
        */

        $deadBirds = (int) FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('dead');

        /*
        |--------------------------------------------------------------------------
        | Available Living Birds
        |--------------------------------------------------------------------------
        */

        $availableBirds = max(
            0,
            $totalBirds - $deadBirds
        );

        /*
        |--------------------------------------------------------------------------
        | All Flock Records
        |--------------------------------------------------------------------------
        */

        $records = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'farm_id',
                'record_date',
                'sick',
                'recovered',
                'dead',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'success' => true,

            'summary' => [
                'registered_birds' => $totalBirds,
                'sick_birds' => $sickBirds,
                'recovered_birds' => $recoveredBirds,
                'dead_birds' => $deadBirds,
                'available_birds' => $availableBirds,
            ],

            'data' => $records,
        ]);
    }

    /**
     * Save a flock health record.
     *
     * The "sick" value received from the app means:
     *
     * NEW sick birds.
     *
     * The stored "sick" value represents:
     *
     * CURRENT sick birds after recovery and death.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'record_date' => [
                'required',
                'date',
            ],

            'sick' => [
                'required',
                'integer',
                'min:0',
            ],

            'recovered' => [
                'required',
                'integer',
                'min:0',
            ],

            'dead' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $farmId = $request->user()->farm_id;

        $newSick = (int) $validated['sick'];
        $recovered = (int) $validated['recovered'];
        $dead = (int) $validated['dead'];

        /*
        |--------------------------------------------------------------------------
        | Registered Birds
        |--------------------------------------------------------------------------
        */

        $farm = FarmSetting::where(
            'farm_id',
            $farmId
        )->first();

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm settings not found.',
            ], 404);
        }

        $totalBirds = (int) $farm->registered_birds;

        /*
        |--------------------------------------------------------------------------
        | Previous Flock State
        |--------------------------------------------------------------------------
        */

        $latestRecord = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->first();

        $previousSick = $latestRecord
            ? (int) $latestRecord->sick
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Total Previous Deaths
        |--------------------------------------------------------------------------
        */

        $previousDead = (int) FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('dead');

        /*
        |--------------------------------------------------------------------------
        | Living Birds Before This Record
        |--------------------------------------------------------------------------
        */

        $livingBirds = max(
            0,
            $totalBirds - $previousDead
        );

        /*
        |--------------------------------------------------------------------------
        | Validate New Sick Birds
        |--------------------------------------------------------------------------
        |
        | Existing sick birds + newly sick birds
        | cannot exceed the living flock.
        |
        */

        $availableForNewSick = max(
            0,
            $livingBirds - $previousSick
        );

        if ($newSick > $availableForNewSick) {
            return response()->json([
                'success' => false,
                'message' =>
                    "New sick birds cannot exceed the available healthy birds ({$availableForNewSick}).",
                'registered_birds' => $totalBirds,
                'previous_sick_birds' => $previousSick,
                'previous_dead_birds' => $previousDead,
                'available_for_new_sick' => $availableForNewSick,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Available Sick Birds
        |--------------------------------------------------------------------------
        |
        | Previous sick + newly sick.
        |
        */

        $availableSick =
            $previousSick
            + $newSick;

        /*
        |--------------------------------------------------------------------------
        | Validate Recovery + Death
        |--------------------------------------------------------------------------
        |
        | Recovery and death can only happen to birds
        | that are currently sick.
        |
        */

        if (($recovered + $dead) > $availableSick) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Recovered and dead birds cannot exceed the available sick birds ({$availableSick}).",
                'available_sick_birds' => $availableSick,
                'requested_recovered' => $recovered,
                'requested_dead' => $dead,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Current Sick Birds
        |--------------------------------------------------------------------------
        */

        $currentSick =
            $availableSick
            - $recovered
            - $dead;

        /*
        |--------------------------------------------------------------------------
        | Final Flock Integrity
        |--------------------------------------------------------------------------
        */

        $newTotalDead =
            $previousDead
            + $dead;

        $newAvailableBirds =
            $totalBirds
            - $newTotalDead;

        if ($newTotalDead > $totalBirds) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Total dead birds cannot exceed the registered birds ({$totalBirds}).",
                'registered_birds' => $totalBirds,
                'total_dead_birds' => $newTotalDead,
            ], 422);
        }

        if ($currentSick > $newAvailableBirds) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Current sick birds cannot exceed the available living birds ({$newAvailableBirds}).",
                'available_birds' => $newAvailableBirds,
                'current_sick_birds' => $currentSick,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Save Record
        |--------------------------------------------------------------------------
        */

        $record = FlockRecord::create([
            'farm_id' => $farmId,
            'record_date' => $validated['record_date'],
            'sick' => $currentSick,
            'recovered' => $recovered,
            'dead' => $dead,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Final Summary
        |--------------------------------------------------------------------------
        */

        $totalRecovered = (int) FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('recovered');

        $totalDead = (int) FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('dead');

        $availableBirds = max(
            0,
            $totalBirds - $totalDead
        );

        return response()->json([
            'success' => true,

            'message' => 'Flock record saved successfully.',

            'data' => $record,

            'summary' => [
                'registered_birds' => $totalBirds,
                'sick_birds' => $currentSick,
                'recovered_birds' => $totalRecovered,
                'dead_birds' => $totalDead,
                'available_birds' => $availableBirds,
            ],
        ], 201);
    }
}

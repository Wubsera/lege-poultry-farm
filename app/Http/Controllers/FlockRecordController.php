<?php

namespace App\Http\Controllers;

use App\Models\FarmSetting;
use App\Models\FlockRecord;
use Illuminate\Http\Request;

class FlockRecordController extends Controller
{
    public function create()
    {
        $farmId = auth()->user()->farm_id;

        /*
        |--------------------------------------------------------------------------
        | Farm Information
        |--------------------------------------------------------------------------
        */

        $farm = FarmSetting::where(
            'farm_id',
            $farmId
        )->first();

        $totalBirds = $farm?->registered_birds ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Latest Flock Status
        |--------------------------------------------------------------------------
        */

        $latestRecord = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Current Sick Birds
        |--------------------------------------------------------------------------
        */

        $sickBirds = $latestRecord
            ? $latestRecord->sick
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Total Recovered Birds
        |--------------------------------------------------------------------------
        */

        $recoveredBirds = FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('recovered');


        /*
        |--------------------------------------------------------------------------
        | Total Deaths
        |--------------------------------------------------------------------------
        */

        $deadBirds = FlockRecord::where(
            'farm_id',
            $farmId
        )->sum('dead');


        /*
        |--------------------------------------------------------------------------
        | Current Available Birds
        |--------------------------------------------------------------------------
        |
        | Sick birds are still alive.
        | Only deaths are deducted.
        |
        */

        $availableBirds = max(
            0,
            $totalBirds - $deadBirds
        );


        return view('flock-records.create', compact(
            'farm',
            'latestRecord',
            'totalBirds',
            'sickBirds',
            'recoveredBirds',
            'deadBirds',
            'availableBirds'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
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

        $farmId = auth()->user()->farm_id;

        $newSick = (int) $request->sick;
        $recovered = (int) $request->recovered;
        $dead = (int) $request->dead;


        /*
        |--------------------------------------------------------------------------
        | Previous Sick Birds
        |--------------------------------------------------------------------------
        */

        $latestRecord = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousSick = $latestRecord
            ? $latestRecord->sick
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Available Sick Birds
        |--------------------------------------------------------------------------
        */

        $availableSick = $previousSick + $newSick;


        /*
        |--------------------------------------------------------------------------
        | Validate Recovery / Death
        |--------------------------------------------------------------------------
        */

        if (($recovered + $dead) > $availableSick) {

            return back()
                ->withInput()
                ->withErrors([
                    'sick' =>
                        "Recovered and Dead birds cannot exceed the available sick birds ({$availableSick})."
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Current Sick Birds
        |--------------------------------------------------------------------------
        */

        $currentSick =
            $availableSick
            - $recovered
            - $dead;


        /*
        |--------------------------------------------------------------------------
        | Save Flock Record
        |--------------------------------------------------------------------------
        */

        FlockRecord::create([
            'farm_id' => $farmId,
            'record_date' => $request->record_date,
            'sick' => $currentSick,
            'recovered' => $recovered,
            'dead' => $dead,
        ]);


        return redirect('/flock-records')
            ->with('success', 'Flock record saved successfully!');
    }
}

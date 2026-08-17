<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmSettingController extends Controller
{
    public function edit()
    {
        $farm = auth()->user()->farm;

        $settings = $farm->setting;

        return view('farm-settings.edit', [
            'farm' => $farm,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
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

        $farm = auth()->user()->farm;

        DB::transaction(function () use ($request, $farm) {

            /*
            |--------------------------------------------------------------------------
            | Update Farm
            |--------------------------------------------------------------------------
            */

            $farm->update([
                'farm_name' => $request->farm_name,
                'registration_date' => $request->registration_date,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Update Farm Settings
            |--------------------------------------------------------------------------
            */

            $farm->setting()->updateOrCreate(
                [
                    'farm_id' => $farm->id,
                ],
                [
                    'registered_birds' => $request->registered_birds,
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]
            );
        });

        return redirect('/farm-settings')
            ->with('success', 'Farm information saved successfully!');
    }
}

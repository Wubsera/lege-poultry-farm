<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmSettingController extends Controller
{
    /**
     * Display farm settings.
     *
     * Both Farm Admin and Staff can view their own farm settings.
     */
    public function edit()
    {
        $farm = auth()->user()->farm;

        if (!$farm) {
            abort(404, 'Farm not found.');
        }

        $settings = $farm->setting;

        return view('farm-settings.edit', [
            'farm' => $farm,
            'settings' => $settings,
        ]);
    }

    /**
     * Update farm settings.
     *
     * Only the Farm Admin can modify farm information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Admin Authorization
        |--------------------------------------------------------------------------
        |
        | Staff users can view Farm Settings, but only the Farm Admin
        | is allowed to update the information.
        |
        */

        if (!$user->is_admin) {
            abort(403, 'Only the farm administrator can update farm settings.');
        }

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

        /*
        |--------------------------------------------------------------------------
        | Get Authenticated User's Farm
        |--------------------------------------------------------------------------
        */

        $farm = $user->farm;

        if (!$farm) {
            abort(404, 'Farm not found.');
        }

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
            ->with(
                'success',
                'Farm information saved successfully!'
            );
    }
}

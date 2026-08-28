<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\FarmSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * The first registered user uses Farm #1.
     * If Farm #1 does not have FarmSetting yet,
     * it will be created automatically.
     *
     * Later users create their own farm and farm settings.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Registration
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:20',
                'unique:users,mobile_number',
            ],

            'pin' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'confirmed',
            ],

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
        | Create Farm + Farm Settings + User
        |--------------------------------------------------------------------------
        */

        $user = DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | First User
            |--------------------------------------------------------------------------
            */

            if (User::count() === 0) {

                /*
                |--------------------------------------------------------------------------
                | Get Existing Farm #1
                |--------------------------------------------------------------------------
                */

                $farm = Farm::find(1);

                /*
                |--------------------------------------------------------------------------
                | Safety: Create Farm #1 if Missing
                |--------------------------------------------------------------------------
                */

                if (!$farm) {

                    $farm = Farm::create([
                        'farm_name' => $request->farm_name,
                        'registration_date' => $request->registration_date,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create or Update Farm Settings
                |--------------------------------------------------------------------------
                */

                $farmSetting = FarmSetting::firstOrNew([
                    'farm_id' => $farm->id,
                ]);

                $farmSetting->registered_birds =
                    $request->registered_birds;

                $farmSetting->farm_name =
                    $request->farm_name;

                $farmSetting->registration_date =
                    $request->registration_date;

                $farmSetting->save();

                /*
                |--------------------------------------------------------------------------
                | Update Farm
                |--------------------------------------------------------------------------
                */

                $farm->update([
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | Later Users: Create New Farm
                |--------------------------------------------------------------------------
                */

                $farm = Farm::create([
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Create Farm Settings
                |--------------------------------------------------------------------------
                */

                FarmSetting::create([
                    'farm_id' => $farm->id,
                    'registered_birds' => $request->registered_birds,
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            return User::create([
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'email' => null,
                'password' => Hash::make($request->pin),
                'farm_id' => $farm->id,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Registration Event
        |--------------------------------------------------------------------------
        */

        event(new Registered($user));

        /*
        |--------------------------------------------------------------------------
        | Login Automatically
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | Redirect to Dashboard
        |--------------------------------------------------------------------------
        */

        return redirect(
            route('dashboard', absolute: false)
        );
    }
}

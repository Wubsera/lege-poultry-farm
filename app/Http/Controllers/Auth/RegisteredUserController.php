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
     * Each registered user belongs to one farm.
     *
     * The first user uses the existing Farm #1.
     * Later users create their own farm.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            /*
            |--------------------------------------------------------------------------
            | User Information
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | PIN
            |--------------------------------------------------------------------------
            |
            | Numeric only.
            | No minimum or maximum length.
            |
            */

            'pin' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'confirmed',
            ],

            /*
            |--------------------------------------------------------------------------
            | Farm Information
            |--------------------------------------------------------------------------
            */

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
        | Create User and Farm
        |--------------------------------------------------------------------------
        */

        $user = DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | First User
            |--------------------------------------------------------------------------
            |
            | The first account uses the existing Farm #1.
            |
            */

            if (User::count() === 0) {

                $farm = Farm::findOrFail(1);

                $farmSetting = FarmSetting::where(
                    'farm_id',
                    $farm->id
                )->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Update Existing Farm
                |--------------------------------------------------------------------------
                */

                $farm->update([
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]);


                $farmSetting->update([
                    'registered_birds' => $request->registered_birds,
                    'farm_name' => $request->farm_name,
                    'registration_date' => $request->registration_date,
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | Create New Farm
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

                /*
                |--------------------------------------------------------------------------
                | Keep Email Empty
                |--------------------------------------------------------------------------
                |
                | Farmers do not use email.
                |
                */

                'email' => null,

                /*
                |--------------------------------------------------------------------------
                | Store PIN Securely
                |--------------------------------------------------------------------------
                */

                'password' => Hash::make($request->pin),

                /*
                |--------------------------------------------------------------------------
                | Connect User to Farm
                |--------------------------------------------------------------------------
                */

                'farm_id' => $farm->id,
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Fire Registration Event
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
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return redirect(
            route('dashboard', absolute: false)
        );
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile_number' => [
                'required',
                'string',
                'max:20',
            ],

            'pin' => [
                'required',
                'string',
            ],
        ]);

        $mobileNumber = trim($request->mobile_number);

        $throttleKey = Str::transliterate(
            Str::lower($mobileNumber) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'mobile_number' => trans(
                    'auth.throttle',
                    [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]
                ),
            ]);
        }

        if (! Auth::attempt([
            'mobile_number' => $mobileNumber,
            'password' => $request->pin,
        ])) {

            RateLimiter::hit(
                $throttleKey,
                60
            );

            throw ValidationException::withMessages([
                'mobile_number' => __('The mobile number or PIN is incorrect.'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

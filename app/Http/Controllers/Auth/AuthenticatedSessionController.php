<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        /*
         * Normalize Ethiopian mobile number.
         *
         * Accepted formats:
         * 09xxxxxxxx
         * 9xxxxxxxx
         * 2519xxxxxxxx
         * +2519xxxxxxxx
         */
        $mobileNumber = $this->normalizeMobileNumber(
            $request->mobile_number
        );

        if ($mobileNumber === null) {
            throw ValidationException::withMessages([
                'mobile_number' => 'Please enter a valid Ethiopian mobile number.',
            ]);
        }

        /*
         * Use the normalized number for rate limiting as well.
         */
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

        /*
         * Find the user by normalized mobile number.
         *
         * We intentionally do not include is_active here.
         * This allows us to distinguish an inactive account
         * from incorrect login credentials.
         */
        $user = User::where(
            'mobile_number',
            $mobileNumber
        )->first();

        /*
         * User does not exist or PIN is incorrect.
         */
        if (
            !$user ||
            !Hash::check(
                $request->pin,
                $user->password
            )
        ) {
            RateLimiter::hit(
                $throttleKey,
                60
            );

            throw ValidationException::withMessages([
                'mobile_number' => __('The mobile number or PIN is incorrect.'),
            ]);
        }

        /*
         * Credentials are correct, but the account is inactive.
         */
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'mobile_number' => __('Account is inactive.'),
            ]);
        }

        /*
         * Credentials are valid and the account is active.
         */
        Auth::login($user);

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }

    /**
     * Normalize Ethiopian mobile number to 09xxxxxxxx format.
     */
    private function normalizeMobileNumber(string $mobileNumber): ?string
    {
        // Remove spaces, hyphens, parentheses and dots.
        $mobileNumber = preg_replace(
            '/[\s\-().]/',
            '',
            $mobileNumber
        );

        // Remove leading +.
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

        // 09xxxxxxxx -> 09xxxxxxxx
        if (preg_match('/^09\d{8}$/', $mobileNumber)) {
            return $mobileNumber;
        }

        return null;
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

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Password reset is not used by Dorofarm.
     *
     * Farmers authenticate using:
     * - Mobile Number
     * - PIN
     */
    public function create(Request $request): View
    {
        abort(404);
    }


    /**
     * Password reset is not used by Dorofarm.
     */
    public function store(Request $request): RedirectResponse
    {
        abort(404);
    }
}

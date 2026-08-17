<?php

namespace App\Services;

use App\Models\Farm;

class CurrentFarm
{
    /*
    |--------------------------------------------------------------------------
    | Current Farm ID
    |--------------------------------------------------------------------------
    |
    | For now, Lege Poultry Farm is Farm #1.
    |
    | Later, this will come from the authenticated user/session.
    |
    */

    protected int $farmId = 1;


    /*
    |--------------------------------------------------------------------------
    | Get Current Farm
    |--------------------------------------------------------------------------
    */

    public function get(): Farm
    {
        return Farm::findOrFail($this->farmId);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Current Farm ID
    |--------------------------------------------------------------------------
    */

    public function id(): int
    {
        return $this->farmId;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSetting extends Model
{
    protected $fillable = [
        'farm_id',
        'farm_name',
        'registered_birds',
        'registration_date',
    ];

    protected $casts = [
        'registration_date' => 'date',
    ];


    /*
    |--------------------------------------------------------------------------
    | Farm
    |--------------------------------------------------------------------------
    */

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}

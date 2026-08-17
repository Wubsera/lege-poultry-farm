<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EggProduction extends Model
{
    protected $fillable = [
        'farm_id',
        'production_date',
        'produced',
        'broken',
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

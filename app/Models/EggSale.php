<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EggSale extends Model
{
    protected $fillable = [
        'farm_id',
        'sale_date',
        'name',
        'quantity',
        'unit_price',
        'total_amount',
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

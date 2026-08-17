<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'farm_id',
        'expense_date',
        'type',
        'description',
        'amount',
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

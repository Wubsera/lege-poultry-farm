<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockRecord extends Model
{
    protected $fillable = [
        'farm_id',
        'record_date',
        'sick',
        'recovered',
        'dead',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}

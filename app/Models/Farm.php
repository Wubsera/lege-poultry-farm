<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    protected $fillable = [
        'farm_name',
        'registration_date',
    ];

    protected $casts = [
        'registration_date' => 'date',
    ];


    /*
    |--------------------------------------------------------------------------
    | Farm Settings
    |--------------------------------------------------------------------------
    */

    public function setting(): HasOne
    {
        return $this->hasOne(FarmSetting::class);
    }

  /*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/

public function users(): HasMany
{
    return $this->hasMany(User::class);
}



    /*
    |--------------------------------------------------------------------------
    | Egg Production
    |--------------------------------------------------------------------------
    */

    public function eggProductions(): HasMany
    {
        return $this->hasMany(EggProduction::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Egg Sales
    |--------------------------------------------------------------------------
    */

    public function eggSales(): HasMany
    {
        return $this->hasMany(EggSale::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Expenses
    |--------------------------------------------------------------------------
    */

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Flock Records
    |--------------------------------------------------------------------------
    */

    public function flockRecords(): HasMany
    {
        return $this->hasMany(FlockRecord::class);
    }
}

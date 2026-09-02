<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

protected $fillable = [
    'name',
    'email',
    'mobile_number',
    'password',
    'farm_id',
    'is_admin',
    'is_active',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
    ];
}

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}

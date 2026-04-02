<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function client_vendor(): HasMany
    {
        return $this->hasMany(Client_vendor::class);
    }

    public function daily_report(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}

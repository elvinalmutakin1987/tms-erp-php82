<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function service_item(): HasMany
    {
        return $this->hasMany(Service_item::class);
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}

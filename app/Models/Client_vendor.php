<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client_vendor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withDefault(['name' => null]);
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function request_quotation(): HasMany
    {
        return $this->hasMany(Request_quotation::class);
    }

    public function purchase_order(): HasMany
    {
        return $this->hasMany(Purchase_order::class);
    }
}

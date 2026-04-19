<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Contract extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function client_vendor(): BelongsTo
    {
        return $this->belongsTo(Client_vendor::class)->withDefault(['name' => null]);
    }

    public function unit_rate(): HasMany
    {
        return $this->hasMany(Unit_rate::class);
    }

    public function contract_rate(): HasMany
    {
        return $this->hasMany(Contract_rate::class);
    }

    public function unit_target(): HasMany
    {
        return $this->hasMany(Unit_target::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withDefault(['name' => null]);
    }
}

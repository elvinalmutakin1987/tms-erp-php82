<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Maintenance_detail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function maintenance_item(): BelongsTo
    {
        return $this->belongsTo(Maintenance_item::class)->withDefault(['name' => null]);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class)->withDefault(['maintenance_no' => null]);
    }
}

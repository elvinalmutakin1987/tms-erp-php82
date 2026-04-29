<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Purchase_order_detail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];


    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(Purchase_order::class);
    }

    public function maintenance_item(): BelongsTo
    {
        return $this->belongsTo(Maintenance_item::class);
    }

    public function mro_item(): BelongsTo
    {
        return $this->belongsTo(Mro_item::class);
    }
}

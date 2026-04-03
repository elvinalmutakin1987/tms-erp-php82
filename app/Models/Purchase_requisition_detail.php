<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class Purchase_requisition_detail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function purchase_requisition(): BelongsTo
    {
        return $this->belongsTo(Purchase_requisition::class)->withDefault(['driver' => null]);
    }

    public function maintenance_item(): BelongsTo
    {
        return $this->belongsTo(Maintenance_item::class)->withDefault(['driver' => null]);
    }

    public function mro_item(): BelongsTo
    {
        return $this->belongsTo(Mro_item::class)->withDefault(['driver' => null]);
    }
}

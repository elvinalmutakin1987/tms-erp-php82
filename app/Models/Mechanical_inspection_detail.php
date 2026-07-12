<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mechanical_inspection_detail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function mechanical_inspection(): BelongsTo
    {
        return $this->belongsTo(Mechanical_inspection::class)->withDefault(['inspector' => null]);
    }
}

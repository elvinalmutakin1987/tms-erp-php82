<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class Mechanical_inspection_detail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mechanical_inspection(): BelongsTo
    {
        return $this->belongsTo(Mechanical_inspection::class)->withDefault(['inspector' => null]);
    }
}

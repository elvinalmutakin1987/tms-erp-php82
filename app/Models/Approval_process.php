<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval_process extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function approval_flow(): BelongsTo
    {
        return $this->belongsTo(Approval_flow::class);
    }
}

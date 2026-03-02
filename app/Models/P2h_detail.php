<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class P2h_detail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function p2h(): BelongsTo
    {
        return $this->belongsTo(P2h::class)->withDefault(['driver' => null]);
    }
}

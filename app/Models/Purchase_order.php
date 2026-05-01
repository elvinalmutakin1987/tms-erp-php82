<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class Purchase_order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    // protected static function booted()
    // {
    //     static::creating(function ($purchase_order) {
    //         $presenter = new DatePrefixPresenter('Y/m', '/');
    //         $purchase_order->order_no = running_number()
    //             ->type('po')
    //             ->formatter($presenter)
    //             ->generate();
    //     });
    // }

    protected static function booted(): void
    {
        static::creating(function ($po) {
            if (!empty($po->order_no)) {
                return;
            }
            $kodeDokumen = 'TMS-SGT';
            $po->order_no = running_number()
                ->type('po')
                ->formatter(new class($kodeDokumen) implements Presenter {
                    public function __construct(
                        private string $kodeDokumen
                    ) {}
                    public function format(string $type, int $number): string
                    {
                        $bulanRomawi = [
                            1  => 'I',
                            2  => 'II',
                            3  => 'III',
                            4  => 'IV',
                            5  => 'V',
                            6  => 'VI',
                            7  => 'VII',
                            8  => 'VIII',
                            9  => 'IX',
                            10 => 'X',
                            11 => 'XI',
                            12 => 'XII',
                        ];
                        $bulan = $bulanRomawi[(int) date('n')];
                        $tahun = date('Y');
                        return sprintf(
                            '%03d/%s/%s/%s',
                            $number,
                            $this->kodeDokumen,
                            $bulan,
                            $tahun
                        );
                    }
                })
                ->generate();
        });
    }

    public function purchase_order_detail(): HasMany
    {
        return $this->hasMany(Purchase_order_detail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['username' => null]);
    }

    public function purchase_requisition(): BelongsTo
    {
        return $this->belongsTo(Purchase_requisition::class)->withDefault(['requisition_no' => null]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class GuideProcedure extends Pivot
{
    protected $with = [
        'guide',
        'procedure'
    ];

    public $incrementing = false;
    public $timestamps = true;
    protected $table = 'guide_procedures';

    protected $fillable = [
        'guide_id',
        'procedure_id',
        'request_amount',
        'permission_amount',
        'execution_date',
        'reduction_factor',
        'unity_price'
    ];

    protected $appends = [
        'execution_date_formatted',
        'unity_price_formatted',
        'total',
        'total_formatted'
    ];

    public function guide()
    {
        return $this->belongsTo(GuideSadt::class, 'guide_id');
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function getExecutionDateFormattedAttribute()
    {
        return !empty($this->attributes['execution_date'])
            ? Carbon::createFromFormat('Y-m-d', $this->attributes['execution_date'])
                ->format('d/m/Y')
            : null;
    }

    public function getUnityPriceFormattedAttribute()
    {
        return 'R$ ' . number_format($this->attributes['unity_price'], 2, ',', '.');
    }

    public function getTotalAttribute()
    {
        return $this->attributes['permission_amount'] * $this->attributes['unity_price'];
    }

    public function getTotalFormattedAttribute()
    {
        return 'R$ ' . number_format($this->getTotalAttribute(), 2, ',', '.');
    }
}

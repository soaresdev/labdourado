<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GuideProcedure extends Pivot
{
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'guide_procedures';

    protected $fillable = [
        'guide_id',
        'procedure_id',
        'request_amount',
        'permission_amount',
        'execution_date',
        'reduction_factor',
        'unity_price',
        'total_price'
    ];
}

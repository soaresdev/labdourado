<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProcedureOperator extends Pivot
{
    protected $with = [
        'procedure',
        'operator',
    ];

    public $incrementing = false;
    public $timestamps = true;
    protected $table = 'procedure_operators';

    protected $fillable = [
        'procedure_id',
        'operator_id',
        'price'
    ];

    protected $appends = [
        'price_formatted'
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function getPriceFormattedAttribute()
    {
        return 'R$ ' . number_format($this->attributes['price'], 2, ',', '.');
    }
}

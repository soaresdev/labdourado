<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProviderOperator extends Pivot
{
    protected $with = [
        'provider',
        'operator'
    ];

    public $incrementing = false;
    public $timestamps = true;
    protected $table = 'provider_operators';

    protected $fillable = [
        'provider_id',
        'operator_id',
        'provider_operator_number'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}

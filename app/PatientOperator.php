<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class PatientOperator extends Pivot
{
    protected $appends = [
        'wallet_expiration_formatted'
    ];

    public function getWalletExpirationFormattedAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['wallet_expiration'])->format('d/m/Y');
    }
}

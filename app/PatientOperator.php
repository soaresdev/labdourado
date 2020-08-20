<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class PatientOperator extends Pivot
{
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'patient_operators';

    protected $fillable = [
        'operator_id',
        'patient_id',
        'wallet_number',
        'wallet_expiration',
    ];

    protected $appends = [
        'wallet_expiration_formatted'
    ];

    public function getWalletExpirationFormattedAttribute()
    {
        return !empty($this->attributes['wallet_expiration']) ? Carbon::createFromFormat('Y-m-d', $this->attributes['wallet_expiration'])->format('d/m/Y') : null;
    }
}

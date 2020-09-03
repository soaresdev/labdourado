<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class PatientOperator extends Pivot
{
    protected $with = [
        'patient',
        'operator',
    ];

    public $incrementing = false;
    public $timestamps = true;
    protected $table = 'patient_operators';

    protected $fillable = [
        'patient_id',
        'operator_id',
        'wallet_number',
        'wallet_expiration'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y',
        'updated_at' => 'datetime:d/m/Y',
    ];

    protected $appends = [
        'wallet_expiration_formatted'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function infos()
    {
        return $this->hasMany(Info::class, 'patient_operator_id');
    }

    public function getWalletExpirationFormattedAttribute()
    {
        return !empty($this->attributes['wallet_expiration']) ? Carbon::createFromFormat('Y-m-d', $this->attributes['wallet_expiration'])
            ->format('d/m/Y') : null;
    }
}

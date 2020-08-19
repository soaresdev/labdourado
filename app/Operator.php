<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'ans',
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

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_operators')->as('doctor_operator')->withPivot('doctor_operator_number');
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_operators')->as('provider_operator')->withPivot('provider_operator_number');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_operators', 'operator_id', 'patient_id')->using(PatientOperator::class)
            ->as('patient_operator')
            ->withPivot([
                'wallet_number',
                'wallet_expiration'
            ]);
    }
}

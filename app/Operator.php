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
        'name',
        'ans',
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
        return $this->belongsToMany(Doctor::class, 'doctor_operators')
            ->using(DoctorOperator::class)
            ->as('doctor_operator')
            ->withPivot([
                'doctor_id',
                'operator_id',
                'doctor_operator_number'
            ])
            ->withTimestamps();
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_operators')
            ->using(ProviderOperator::class)
            ->as('provider_operator')
            ->withPivot([
                'provider_id',
                'operator_id',
                'provider_operator_number'
            ])
            ->withTimestamps();
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_operators')
            ->using(PatientOperator::class)
            ->as('patient_operator')
            ->withPivot([
                'patient_id',
                'operator_id',
                'wallet_number',
                'wallet_expiration'
            ])
            ->withTimestamps();
    }

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'procedure_operators')
            ->using(ProcedureOperator::class)
            ->as('procedure_operator')
            ->withPivot([
                'procedure_id',
                'operator_id',
                'price'
            ])
            ->withTimestamps();
    }
}

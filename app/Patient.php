<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $with = ['operators'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'cns'
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

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'patient_operators', 'patient_id', 'operator_id')->using(PatientOperator::class)
            ->as('patient_operator')
            ->withPivot([
                'wallet_number',
                'wallet_expiration',
            ]);
    }
}

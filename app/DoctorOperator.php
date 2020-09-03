<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DoctorOperator extends Pivot
{
    protected $with = [
        'doctor',
        'operator'
    ];

    public $incrementing = false;
    public $timestamps = true;
    protected $table = 'doctor_operators';

    protected $fillable = [
        'doctor_id',
        'operator_id',
        'doctor_operator_number'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}

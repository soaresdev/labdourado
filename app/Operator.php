<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Operator extends Model
{
    use SoftDeletes;
    use LaravelVueDatatableTrait;
    protected $dataTableColumns = [
        'id' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'name' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'ans' => [
            'searchable' => true,
            'orderable' => true,
        ],
    ];

    protected $dataTableRelationships = [
        "belongsToMany" => [
            "patients" => [
                "model" => Patient::class,
                "pivot" => [
                    "table_name" => "patient_operators",
                    "primary_key" => "id",
                    "foreign_key" => "patient_id",
                    "local_key" => "operator_id",
                ],
                "columns" => [
                    'name' => [
                        'searchable' => true,
                        'orderable' => true,
                    ],
                ]
            ],
        ]
    ];

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

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_operators')->as('doctor_operator')->withPivot('doctor_operator_number');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_operators')->using(PatientOperator::class)
            ->as('patient_operator')
            ->withPivot([
                'wallet_number',
                'wallet_expiration'
            ]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Patient extends Model
{
    use SoftDeletes;
    use LaravelVueDatatableTrait;

    protected $with = ['operators'];

    protected $dataTableColumns = [
        'id' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'name' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'cns' => [
            'searchable' => true,
            'orderable' => true,
        ],
    ];

    protected $dataTableRelationships = [
        "belongsToMany" => [
            "operators" => [
                "model" => Operator::class,
                "pivot" => [
                    "table_name" => "patient_operators",
                    "primary_key" => "id",
                    "foreign_key" => "operator_id",
                    "local_key" => "patient_id",
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
        return $this->belongsToMany(Operator::class, 'patient_operators')->using(PatientOperator::class)
            ->as('patient_operator')
            ->withPivot([
                'wallet_number',
                'wallet_expiration',
            ]);
    }
}

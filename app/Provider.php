<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;
class Provider extends Model
{
    use LaravelVueDatatableTrait;
    protected $with = ['operators'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dataTableColumns = [
        'id' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'name' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'cnes' => [
            'searchable' => true,
            'orderable' => true,
        ]
    ];
    protected $dataTableRelationships = [
        "belongsToMany" => [
            "operators" => [
                "model" => Operator::class,
                "pivot" => [
                    "table_name" => "provider_operators",
                    "primary_key" => "id",
                    "foreign_key" => "operator_id",
                    "local_key" => "provider_id",
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

    protected $fillable = [
        'name', 'cnes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'provider_operators')->as('provider_operator')->withPivot('provider_operator_number');
    }
}

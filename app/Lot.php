<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;
class Lot extends Model
{
    use SoftDeletes, LaravelVueDatatableTrait;

    protected $with = ['operators'];

    protected $dataTableColumns = [
        'id' => [
            'searchable' => false,
            'orderable' => false,
        ],
        'number' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'closed_at' => [
            'searchable' => true,
            'orderable' => true,
        ],
    ];

    protected $dataTableRelationships = [
        "belongsToMany" => [
            "operators" => [
                "model" => Operator::class,
                "pivot" => [
                    "table_name" => "lot_operators",
                    "primary_key" => "id",
                    "foreign_key" => "operator_id",
                    "local_key" => "lot_id",
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
        'number', 'closed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'closed_at' => 'datetime:d/m/Y',
        'created_at' => 'datetime:d/m/Y',
        'updated_at' => 'datetime:d/m/Y',
    ];

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'lot_operators')->as('lot_operator');
    }

    public function guides()
    {
        return $this->hasMany(GuideSadt::class);
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Doctor extends Model
{
    use SoftDeletes, LaravelVueDatatableTrait;
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
        'cp' => [
            'searchable' => true,
            'orderable' => false,
        ],
        'advice_number' => [
            'searchable' => true,
            'orderable' => false,
        ],
        'uf' => [
            'searchable' => true,
            'orderable' => false,
        ],
        'cbo' => [
            'searchable' => true,
            'orderable' => false,
        ],
    ];

    protected $dataTableRelationships = [
        "belongsToMany" => [
            "operators" => [
                "model" => Operator::class,
                "pivot" => [
                    "table_name" => "doctor_operators",
                    "primary_key" => "id",
                    "foreign_key" => "operator_id",
                    "local_key" => "doctor_id",
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
        'name', 'cp', 'advice_number', 'uf', 'cbo'
    ];

    protected $appends = [
        'cp_formatted',
        'uf_formatted'
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
        return $this->belongsToMany(Operator::class, 'doctor_operators')->as('doctor_operator')->withPivot('doctor_operator_number');
    }

    public function getCpFormattedAttribute()
    {
        switch ($this->attributes['cp']) {
            case '01':
                return 'CRAS';
                break;
            case '02':
                return 'COREN';
                break;
            case '03':
                return 'CRF';
                break;
            case '04':
                return 'CRFA';
                break;
            case '05':
                return 'CREFITO';
                break;
            case '06':
                return 'CRM';
                break;
            case '07':
                return 'CRN';
                break;
            case '08':
                return 'CRO';
                break;
            case '09':
                return 'CRP';
                break;
            case '10':
                return 'OUT';
                break;
        }
    }

    public function getUfFormattedAttribute()
    {
        switch ($this->attributes['uf']) {
            case '11':
                return 'RO';
                break;
            case '12':
                return 'AC';
                break;
            case '13':
                return 'AM';
                break;
            case '14':
                return 'RR';
                break;
            case '15':
                return 'PA';
                break;
            case '16':
                return 'AP';
                break;
            case '17':
                return 'TO';
                break;
            case '21':
                return 'MA';
                break;
            case '22':
                return 'PI';
                break;
            case '23':
                return 'CE';
                break;
            case '24':
                return 'RN';
                break;
            case '25':
                return 'PB';
                break;
            case '26':
                return 'PE';
                break;
            case '27':
                return 'AL';
                break;
            case '28':
                return 'SE';
                break;
            case '29':
                return 'BA';
                break;
            case '31':
                return 'MG';
                break;
            case '32':
                return 'ES';
                break;
            case '33':
                return 'RJ';
                break;
            case '35':
                return 'SP';
                break;
            case '41':
                return 'PR';
                break;
            case '42':
                return 'SC';
                break;
            case '43':
                return 'RS';
                break;
            case '50':
                return 'MS';
                break;
            case '51':
                return 'MT';
                break;
            case '52':
                return 'GO';
                break;
            case '53':
                return 'DF';
                break;
            case '98':
                return 'EX';
                break;
        }
    }
}

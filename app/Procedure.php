<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Procedure extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table',
        'number',
        'description'
    ];

    protected $appends = [
        'wrapped'
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

    public function guides()
    {
        return $this->belongsToMany(GuideSadt::class, 'guide_procedures', 'procedure_id', 'guide_id')
            ->using(GuideProcedure::class)->as('guide_procedure')
            ->withPivot([
                'guide_id',
                'procedure_id',
                'execution_date',
                'reduction_factor',
                'request_amount',
                'permission_amount',
                'unity_price'
            ])
            ->withTimestamps();
    }

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'procedure_operators')
            ->using(ProcedureOperator::class)
            ->as('procedure_operator')
            ->withPivot([
                'procedure_id',
                'operator_id',
                'price'
            ])
            ->withTimestamps();
    }

    public function getWrappedAttribute() {
        // return $this->number . ' - ' . Str::words($this->description, 4, '...');
        return $this->number . ' - ' . $this->description;
    }
}

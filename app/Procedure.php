<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table', 'number', 'description'
    ];

    public function guides()
    {
        return $this->belongsToMany(GuideSadt::class, 'guide_procedures', 'procedure_id', 'guide_id')->using(GuideProcedure::class)
            ->as('guide_procedure')
            ->withPivot([
                'request_amount',
                'permission_amount',
                'reduction_factor',
                'execution_date',
                'unity_price',
                'total_price'
            ]);
    }
}

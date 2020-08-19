<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Provider extends Model
{
    use SoftDeletes;
    protected $with = ['operators'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'cnes',
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
        return $this->belongsToMany(Operator::class, 'provider_operators')->as('provider_operator')->withPivot('provider_operator_number');
    }
}

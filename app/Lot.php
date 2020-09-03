<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Lot extends Model
{
    use SoftDeletes;

    protected $withCount = ['guides'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'closed_at'
    ];

    protected $appends = [
        'closed_at_formatted',
        'total',
        'total_formatted'
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

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function guides()
    {
        return $this->hasMany(GuideSadt::class);
    }

    public function getClosedAtFormattedAttribute()
    {
        return !empty($this->attributes['closed_at'])
            ? Carbon::createFromFormat('Y-m-d', $this->attributes['closed_at'])
                ->format('d/m/Y')
            : null;
    }

    public function getTotalAttribute()
    {
        $total = 0;
        foreach ($this->guides()->get() as $guide) {
            $total += !empty($guide->total) ? $guide->total : 0;
        }
        return $total;
    }

    public function getTotalFormattedAttribute()
    {
        $total = 0;
        foreach ($this->guides()->get() as $guide) {
            $total += !empty($guide->total) ? $guide->total : 0;
        }
        return 'R$' . number_format($total, 2, ',', '.');
    }

}

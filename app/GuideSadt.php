<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GuideSadt extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_number',
        'main_number',
        'permission_date',
        'password',
        'password_expiration',
        'guide_operator_number',
        'rn',
        'character_treatment',
        'request_date',
        'clinical_indication',
        'type_treatment',
        'accident_indication',
        'total',
        'observation',
        'lot_id',
        'doctor_id',
        'patient_id',
        'provider_id',
    ];

    protected $appends = [
        'permission_date_formatted',
        'password_expiration_formatted',
        'rn_formatted',
        'character_treatment_formatted',
        'request_date_formatted',
        'type_treatment_formatted',
        'accident_indication_formatted',
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

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'guide_procedures', 'guide_id', 'procedure_id')
            ->using(GuideProcedure::class)
            ->as('guide_procedure')
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

    public function getRnFormattedAttribute()
    {
        return Str::is('N', $this->attributes['rn']) ? 'N - Não' : 'S - Sim';
    }

    public function getCharacterTreatmentFormattedAttribute()
    {
        return Str::is('1', $this->attributes['character_treatment']) ? '1 - Efetiva' : '2 - Urgência/Emergência';
    }

    public function getTypeTreatmentFormattedAttribute()
    {
        switch ($this->attributes['type_treatment']) {
            case '01':
                return '01 - Remoção';
                break;
            case '02':
                return '02 - Pequena Cirurgia';
                break;
            case '03':
                return '03 - Terapias';
                break;
            case '04':
                return '04 - Consulta';
                break;
            case '05':
                return '05 - Exames';
                break;
            case '06':
                return '06 - Atendimento Domiciliar';
                break;
            case '07':
                return '07 - Internação';
                break;
            case '08':
                return '08 - Quimioterapia';
                break;
            case '09':
                return '09 - Radioterapia';
                break;
            case '10':
                return '10 - Terapia Renal Substitutiva (TRS)';
                break;
            case '11':
                return '11 - Pronto Socorro';
                break;
            case '13':
                return '13 - Pequenos atendimentos';
                break;
            case '14':
                return '14 - Admissional';
                break;
            case '15':
                return '15 - Demissional';
                break;
            case '16':
                return '16 - Periódico';
                break;
            case '17':
                return '17 - Retorno ao trabalho';
                break;
            case '18':
                return '18 - Mudança de função';
                break;
            case '19':
                return '19 - Promoção a saúde';
                break;
            case '20':
                return '20 - Beneficiário novo';
                break;
            case '21':
                return '21 - Assistência a demitidos';
                break;
            default:
                return '22 - Telessaude';
        }
    }

    public function getAccidentIndicationFormattedAttribute()
    {
        switch ($this->attributes['accident_indication']) {
            case '0':
                return '0 - Trabalho';
                break;
            case '1':
                return '1 - Trânsito';
                break;
            case '2':
                return '2 - Outros acidentes';
                break;
            default:
                return '9 - Não acidentes';
        }
    }

    public function getPermissionDateFormattedAttribute()
    {
        return !empty($this->attributes['permission_date']) ?
            Carbon::createFromFormat('Y-m-d', $this->attributes['permission_date'])->format('d/m/Y')
            : null;
    }

    public function getPasswordExpirationFormattedAttribute()
    {
        return !empty($this->attributes['password_expiration']) ?
            Carbon::createFromFormat('Y-m-d', $this->attributes['password_expiration'])->format('d/m/Y')
            : null;
    }

    public function getRequestDateFormattedAttribute()
    {
        return !empty($this->attributes['request_date']) ?
            Carbon::createFromFormat('Y-m-d', $this->attributes['request_date'])->format('d/m/Y')
            : null;
    }

    public function getTotalFormattedAttribute()
    {
        return 'R$ ' . number_format($this->attributes['total'], 2, ',', '.');
    }
}

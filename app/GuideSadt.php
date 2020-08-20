<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideSadt extends Model
{
    use SoftDeletes;

    protected $with = ['lot', 'patient', 'doctor', 'provider'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "lot_id",
        "doctor_id",
        "provider_id",
        "patient_id",
        'provider_number',
        'main_number',
        'permission_date',
        'password',
        'password_expiration',
        'guide_operator_number',
        'rn',
        'type_requester',
        'character_treatment',
        'request_date',
        'clinical_indication',
        'type_treatment',
        'accident_indication',
        'total'
    ];

    protected $appends = [
        'rn_formatted',
        'character_treatment_formatted',
        'type_treatment_formatted',
        'accident_indication_formatted',
        'total_formatted',
        'total_guide'
    ];

    public function getRnFormattedAttribute()
    {
        switch ($this->attributes['rn']) {
            case 'N':
                return 'N - Não';
                break;
            case 'S':
                return 'S - Sim';
                break;
        }
    }

    public function getCharacterTreatmentFormattedAttribute()
    {
        switch ($this->attributes['character_treatment']) {
            case '1':
                return '1 - Eletiva';
                break;
            case '2':
                return '2 - Urgência/Emergência';
                break;
        }
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
            case '22':
                return '22 - Telessaude';
                break;
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
            case '9':
                return '9 - Não acidentes';
                break;
        }
    }

    public function getTotalGuideAttribute()
    {
        if (!empty($this->attributes['total'])) {
            return number_format($this->attributes['total'], 2, ',', '.');
        }
        return null;
    }

    public function getTotalFormattedAttribute()
    {
        return 'R$ ' . number_format(empty($this->attributes['total']) ? 0 : $this->attributes['total'], 2, ',', '.');
    }

    public function setTotalAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['total'] = floatval(str_replace(',', '.', str_replace('.', '', $value)));
        }
    }

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
}

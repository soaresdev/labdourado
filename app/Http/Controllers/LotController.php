<?php

namespace App\Http\Controllers;

use App\Lot;
use App\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Spatie\ArrayToXml\ArrayToXml;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $closed = $request->input('closed');
        $query = Lot::whereHas('operator', function ($q) use ($operator, $searchValue, $closed) {
            $q->where('operators.id', !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
            if(isset($closed)) {
                if($closed === "1") {
                    $q->whereNotNull('lots.closed_at');
                } else {
                    $q->whereNull('closed_at');
                }
            }
            $q->where(function ($q2) use ($searchValue) {
                $q2->where("lots.id", "LIKE", "%$searchValue%")
                    ->orWhere("lots.number", "LIKE", "%$searchValue%");
            });
        });
        $query->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function indexData(Request $request, int $id)
    {
        return $this->message->info()->setData(Lot::whereHas('operator', function ($query) use ($id) {
            $query->where('operators.id', $id);
        })->with([
            'operator.doctors',
            'operator.patients',
            'operator.providers',
        ])->where('lots.closed_at', null)->get()->all())->getResponse();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only([
            'number',
            'operator'
        ]), [
            'number' => 'required',
            'operator' => 'required|exists:operators,id'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $operator = Operator::find($validator->validated()['operator']);
            $operator->lots()->create([
                'number' => $validator->validated()['number']
            ]);
            return $this->message->success('Lote' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->only([
            'number',
        ]), [
            'number' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $lot = Lot::findOrFail($id);
            $lot->update([
                'number' => $validator->validated()['number']
            ]);
            return $this->message->success('Lote' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $lot = Lot::findOrFail($id);
            $lot->delete();
            return $this->message->success('Lote' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function toggleStatus(Request $request, int $id)
    {
        try {
            $lot = Lot::findOrFail($id);
            if (!empty($lot->closed_at)) {
                $lot->closed_at = null;
                $lot->save();
            } else {
                $lot->closed_at = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('Y-m-d');
                $lot->save();
            }
            return $this->message->success('Alteração de status do faturamento realizada com sucesso')->setData([$lot])->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function xml(Request $request, int $id)
    {
        try {
            $lot = Lot::findOrFail($id)->with([
                'operator',
                'guides'
            ])->get()->first();
            $registro = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $dia = $registro->format('Y-m-d');
            $hora = $registro->format('H:i:s');
            $hash = "ENVIO_LOTE_GUIAS" . $lot->number . $dia . $hora
                . $lot->guides->first()->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number
                . $lot->operator->ans . "3.05.00" . $lot->number;
            $array = [
                'ans:cabecalho' => [
                    'ans:identificacaoTransacao' => [
                        'ans:tipoTransacao' => 'ENVIO_LOTE_GUIAS',
                        'ans:sequencialTransacao' => $lot->number,
                        'ans:dataRegistroTransacao' => $dia,
                        'ans:horaRegistroTransacao' => $hora
                    ],
                    'ans:origem' => [
                        'ans:identificacaoPrestador' => [
                            'ans:codigoPrestadorNaOperadora' => $lot->guides->first()->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number
                        ]
                    ],
                    'ans:destino' => [
                        'ans:registroANS' => $lot->operator->ans
                    ],
                    'ans:Padrao' => '3.05.00'
                ],
                'ans:prestadorParaOperadora' => [
                    'ans:loteGuias' => [
                        'ans:numeroLote' => $lot->number,
                        'ans:guiasTISS' => []
                    ],
                ],
                'ans:epilogo' => [
                    'ans:hash' => ''
                ]
            ];
            $cont = 0;
            foreach ($lot->guides as $guide) {
                $total = !empty($guide->total) ? $guide->total : '0.00';
                $hash .= $lot->operator->ans . $guide->provider_number . $guide->main_number . $guide->guide_operator_number . $guide->permission_date . $guide->password . $guide->password_expiration
                    . $guide->patient->operators->where('patient_operator.operator_id', $lot->operator->id)->first()->patient_operator->wallet_number . $guide->rn
                    . Str::upper(Str::slug($guide->patient->name, ' ')) . Str::upper(Str::slug($guide->patient->cns, ' ')) . $guide->doctor->operators->where('doctor_operator.operator_id', $lot->operator->id)->first()->doctor_operator->doctor_operator_number
                    . Str::upper(Str::slug($guide->doctor->name, ' ')) . Str::upper(Str::slug($guide->doctor->name, ' ')) . $guide->doctor->cp . $guide->doctor->advice_number . $guide->doctor->uf . $guide->doctor->cbo
                    . $guide->request_date . $guide->character_treatment . Str::upper(Str::slug($guide->clinical_indication, ' ')) . $guide->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number
                    . Str::upper(Str::slug($guide->provider->name, ' ')) . $guide->provider->cnes . $guide->type_treatment . $guide->accident_indication . Str::upper(Str::slug($guide->observation, ' ')) . $total;
                $array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][] = [
                    'ans:cabecalhoGuia' => [
                        'ans:registroANS' => $lot->operator->ans,
                        'ans:numeroGuiaPrestador' => $guide->provider_number,
                        'ans:guiaPrincipal' => $guide->main_number
                    ],
                    'ans:dadosAutorizacao' => [
                        'ans:numeroGuiaOperadora' => $guide->guide_operator_number,
                        'ans:dataAutorizacao' => $guide->permission_date,
                        'ans:senha' => $guide->password,
                        'ans:dataValidadeSenha' => $guide->password_expiration,
                    ],
                    'ans:dadosBeneficiario' => [
                        'ans:numeroCarteira' => $guide->patient->operators->where('patient_operator.operator_id', $lot->operator->id)->first()->patient_operator->wallet_number,
                        'ans:atendimentoRN' => $guide->rn,
                        'ans:nomeBeneficiario' => Str::upper(Str::slug($guide->patient->name, ' ')),
                        'ans:numeroCNS' => Str::upper(Str::slug($guide->patient->cns, ' '))
                    ],
                    'ans:dadosSolicitante' => [
                        'ans:contratadoSolicitante' => [
                            'ans:codigoPrestadorNaOperadora' => $guide->doctor->operators->where('doctor_operator.operator_id', $lot->operator->id)->first()->doctor_operator->doctor_operator_number,
                            'ans:nomeContratado' => Str::upper(Str::slug($guide->doctor->name, ' '))
                        ],
                        'ans:profissionalSolicitante' => [
                            'ans:nomeProfissional' => Str::upper(Str::slug($guide->doctor->name, ' ')),
                            'ans:conselhoProfissional' => $guide->doctor->cp,
                            'ans:numeroConselhoProfissional' => $guide->doctor->advice_number,
                            'ans:UF' => $guide->doctor->uf,
                            'ans:CBOS' => $guide->doctor->cbo
                        ]
                    ],
                    'ans:dadosSolicitacao' => [
                        'ans:dataSolicitacao' => $guide->request_date,
                        'ans:caraterAtendimento' => $guide->character_treatment,
                        'ans:indicacaoClinica' => Str::upper(Str::slug($guide->clinical_indication, ' '))
                    ],
                    'ans:dadosExecutante' => [
                        'ans:contratadoExecutante' => [
                            'ans:codigoPrestadorNaOperadora' => $guide->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number,
                            'ans:nomeContratado' => Str::upper(Str::slug($guide->provider->name, ' '))
                        ],
                        'ans:CNES' => $guide->provider->cnes
                    ],
                    'ans:dadosAtendimento' => [
                        'ans:tipoAtendimento' => $guide->type_treatment,
                        'ans:indicacaoAcidente' => $guide->accident_indication
                    ],
                    'ans:observacao' => $guide->observation,
                    'ans:valorTotal' => [
                        'ans:valorTotalGeral' => !empty($guide->total) ? $guide->total : '0.00'
                    ]
                ];
                if (empty($guide->request_date)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosSolicitacao']['ans:dataSolicitacao']);
                }
                if(empty($guide->clinical_indication)){
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosSolicitacao']['ans:indicacaoClinica']);
                }
                if(empty($guide->main_number)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:cabecalhoGuia']['ans:guiaPrincipal']);
                }
                if(empty($guide->guide_operator_number)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:numeroGuiaOperadora']);
                }
                if(empty($guide->password)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:senha']);
                }
                if(empty($guide->password_expiration)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:dataValidadeSenha']);
                }
                if(empty($guide->patient->cns)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosBeneficiario']['ans:numeroCNS']);
                }
                if(empty($guide->observation)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:observacao']);
                }
                $cont++;
            }
            $array['ans:epilogo']['ans:hash'] = md5($hash);
            $result = ArrayToXml::convert($array, [
                'rootElementName' => 'ans:mensagemTISS',
                '_attributes' => [
                    'xmlns:ans' => 'http://www.ans.gov.br/padroes/tiss/schemas',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.ans.gov.br/padroes/tiss/schemas http://www.ans.gov.br/padroes/tiss/schemas/tissV3_05_00.xsd'
                ]
            ], true, 'ISO-8859-1', '1.0', ['formatOutput' => true]);
            $path = '/xmls/lot/' . $lot->id . '/';
            $filename = $lot->number . '_' . $array['ans:epilogo']['ans:hash'] . '.xml';
            Storage::makeDirectory($path);
            if (Storage::put($path . $filename, $result)) {
                return $this->message->success()->setData(['url' => Storage::url($path . $filename)])->getResponse();
            }
            return $this->message->error()
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}

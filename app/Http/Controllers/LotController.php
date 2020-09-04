<?php

namespace App\Http\Controllers;

use App\Lot;
use App\Operator;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    protected $fillable = [
        'number',
        'closed_at',
        'operator'
    ];

    protected $rulesCreate = [
        'number' => 'required|max:12',
        'operator' => 'required|exists:operators,id'
    ];

    protected $rulesUpdate = [
        'number' => 'required|max:12'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $closed = $request->input('closed');
        $query = Lot::with(['operator' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('operator', function ($sql2) use ($operator, $closed, $searchValue, $orderBy, $sortBy) {
            $sql2->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            if (isset($closed)) {
                if ($closed === "1") {
                    $sql2->whereNotNull('lots.closed_at');
                } else {
                    $sql2->whereNull('lots.closed_at');
                }
            }
            $sql2->where(function ($sql3) use ($closed, $searchValue) {
                $sql3->where("lots.id", "LIKE", "%$searchValue%")
                    ->orWhere("lots.number", "LIKE", "%$searchValue%");
            });
        });
        if (!Str::is('guides_count', $sortBy) && !Str::is('total', $sortBy)) {
            $query->orderBy($sortBy, $orderBy);
        }
        $data = $query->paginate($length);
        if (Str::is('guides_count', $sortBy)) {
            $data->setCollection(
                collect(
                    Str::is('desc', $orderBy) ?
                        collect($data->items())->sortByDesc($sortBy)
                        : collect($data->items())->sortBy($sortBy)
                )->values()
            );
        } else if (Str::is('total', $sortBy)) {
            $data->setCollection(
                collect(
                    Str::is('desc', $orderBy) ?
                        collect($data->items())->sortByDesc($sortBy)
                        : collect($data->items())->sortBy($sortBy)
                )->values()
            );
        }
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
            'operator.procedures'
        ])->whereNull('lots.closed_at')->get()->all())->getResponse();
    }

    public function store(Request $request)
    {
        $data = $request->only($this->fillable);
        $validator = Validator::make($data, $this->rulesCreate);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $operator = Operator::with('lots')->findOrFail($data['operator']);
            $lot = $operator->lots()->create($data);
            return $this->message->success('Lote' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData($lot->toArray())
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Lote não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function update(Request $request, int $id)
    {
        $data = $request->only($this->fillable);
        $validator = Validator::make($data, $this->rulesUpdate);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $lot = Lot::with('operator')->findOrFail($id);
            $lot->update($data);
            return $this->message->success('Lote' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Lote não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
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
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Lote não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function toggleStatus(Request $request, int $id)
    {
        try {
            $lot = Lot::with('operator')->findOrFail($id);
            if (!empty($lot->closed_at)) {
                $lot->closed_at = null;
                $lot->save();
            } else {
                $lot->closed_at = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('Y-m-d');
                $lot->save();
            }
            return $this->message->success('Alteração de status do faturamento realizada com sucesso')->setData($lot->toArray())->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Lote não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function export()
    {
        $data = Lot::with('operator:id,name,ans')->get();
        $moment = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis');
        $filename = "lotes_$moment.pdf";
        view()->share('lots', $data);
        $pdf = SnappyPdf::loadView('exports.lots', $data);
        return $pdf->download($filename);
    }

    public function xml(Request $request, int $id)
    {
        try {
            $lot = Lot::with([
                'operator',
                'guides'
            ])->findOrFail($id);
            $registro = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $dia = Str::of(Str::ascii($registro->format('Y-m-d')))->upper()->trim();
            $hora = Str::of(Str::ascii($registro->format('H:i:s')))->upper()->trim();
            $lotNumber = Str::of(Str::ascii($lot->number))->upper()->trim();
            $operatorANS = Str::of(Str::ascii($lot->operator->ans))->upper()->trim();
            $providerOperatorN = Str::of(Str::ascii($lot->guides->first()->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number))->upper()->trim();
            $hash = "ENVIO_LOTE_GUIAS" . $lotNumber . $dia . $hora . $providerOperatorN . $operatorANS . "3.05.00" . $lotNumber;
            $array = [
                'ans:cabecalho' => [
                    'ans:identificacaoTransacao' => [
                        'ans:tipoTransacao' => 'ENVIO_LOTE_GUIAS',
                        'ans:sequencialTransacao' => $lotNumber,
                        'ans:dataRegistroTransacao' => $dia,
                        'ans:horaRegistroTransacao' => $hora
                    ],
                    'ans:origem' => [
                        'ans:identificacaoPrestador' => [
                            'ans:codigoPrestadorNaOperadora' => $providerOperatorN
                        ]
                    ],
                    'ans:destino' => [
                        'ans:registroANS' => $operatorANS
                    ],
                    'ans:Padrao' => '3.05.00'
                ],
                'ans:prestadorParaOperadora' => [
                    'ans:loteGuias' => [
                        'ans:numeroLote' => $lotNumber,
                        'ans:guiasTISS' => []
                    ],
                ],
                'ans:epilogo' => [
                    'ans:hash' => ''
                ]
            ];
            $cont = 0;
            foreach ($lot->guides as $guide) {
                $ans = Str::of(Str::ascii($lot->operator->ans))->upper()->trim();
                $providerNumber = Str::of(Str::ascii($guide->provider_number))->upper()->trim();
                $mainNumber = Str::of(Str::ascii($guide->main_number))->upper()->trim();
                $guideOperatorNumber = Str::of(Str::ascii($guide->guide_operator_number))->upper()->trim();
                $permissionDate = Str::of(Str::ascii($guide->permission_date))->upper()->trim();
                $password = Str::of(Str::ascii($guide->password))->upper()->trim();
                $passwordExpiration = Str::of(Str::ascii($guide->password_expiration))->upper()->trim();
                $walletNumber = Str::of(Str::ascii($guide->patient->operators->where('patient_operator.operator_id', $lot->operator->id)->first()->patient_operator->wallet_number))->upper()->trim();
                $rn = Str::of(Str::ascii($guide->rn))->upper()->trim();
                $patientName = Str::of(Str::ascii($guide->patient->name))->upper()->trim();
                $patientCNS = Str::of(Str::ascii($guide->patient->cns))->upper()->trim();
                $doctorOperatorNumber = Str::of(Str::ascii($guide->doctor->operators->where('doctor_operator.operator_id', $lot->operator->id)->first()->doctor_operator->doctor_operator_number))->upper()->trim();
                $doctorName = Str::of(Str::ascii($guide->doctor->name))->upper()->trim();
                $doctorCp = Str::of(Str::ascii($guide->doctor->cp))->upper()->trim();
                $doctorAdviceNumber = Str::of(Str::ascii($guide->doctor->advice_number))->upper()->trim();
                $doctorUf = Str::of(Str::ascii($guide->doctor->uf))->upper()->trim();
                $doctorCbo = Str::of(Str::ascii($guide->doctor->cbo))->upper()->trim();
                $requestDate = Str::of(Str::ascii($guide->request_date))->upper()->trim();
                $characterTreatment = Str::of(Str::ascii($guide->character_treatment))->upper()->trim();
                $clinicalIndication = Str::of(Str::ascii($guide->clinical_indication))->upper()->trim();
                $providerOperatorNumber = Str::of(Str::ascii($guide->provider->operators->where('provider_operator.operator_id', $lot->operator->id)->first()->provider_operator->provider_operator_number))->upper()->trim();
                $providerName = Str::of(Str::ascii($guide->provider->name))->upper()->trim();
                $providerCNES = Str::of(Str::ascii($guide->provider->cnes))->upper()->trim();
                $typeTreatment = Str::of(Str::ascii($guide->type_treatment))->upper()->trim();
                $accidentIndication = Str::of(Str::ascii($guide->accident_indication))->upper()->trim();
                $observation = Str::of(Str::ascii($guide->observation))->upper()->trim();
                $hash .= $ans . $providerNumber . $mainNumber . $guideOperatorNumber . $permissionDate . $password . $passwordExpiration
                    . $walletNumber . $rn . $patientName . $patientCNS . $doctorOperatorNumber . $doctorName . $doctorName
                    . $doctorCp . $doctorAdviceNumber . $doctorUf . $doctorCbo . $requestDate . $characterTreatment . $clinicalIndication
                    . $providerOperatorNumber . $providerName . $providerCNES . $typeTreatment . $accidentIndication;
                $array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][] = [
                    'ans:cabecalhoGuia' => [
                        'ans:registroANS' => $ans,
                        'ans:numeroGuiaPrestador' => $providerNumber,
                        'ans:guiaPrincipal' => $mainNumber
                    ],
                    'ans:dadosAutorizacao' => [
                        'ans:numeroGuiaOperadora' => $guideOperatorNumber,
                        'ans:dataAutorizacao' => $permissionDate,
                        'ans:senha' => $password,
                        'ans:dataValidadeSenha' => $passwordExpiration,
                    ],
                    'ans:dadosBeneficiario' => [
                        'ans:numeroCarteira' => $walletNumber,
                        'ans:atendimentoRN' => $rn,
                        'ans:nomeBeneficiario' => $patientName,
                        'ans:numeroCNS' => $patientCNS
                    ],
                    'ans:dadosSolicitante' => [
                        'ans:contratadoSolicitante' => [
                            'ans:codigoPrestadorNaOperadora' => $doctorOperatorNumber,
                            'ans:nomeContratado' => $doctorName
                        ],
                        'ans:profissionalSolicitante' => [
                            'ans:nomeProfissional' => $doctorName,
                            'ans:conselhoProfissional' => $doctorCp,
                            'ans:numeroConselhoProfissional' => $doctorAdviceNumber,
                            'ans:UF' => $doctorUf,
                            'ans:CBOS' => $doctorCbo
                        ]
                    ],
                    'ans:dadosSolicitacao' => [
                        'ans:dataSolicitacao' => $requestDate,
                        'ans:caraterAtendimento' => $characterTreatment,
                        'ans:indicacaoClinica' => $clinicalIndication
                    ],
                    'ans:dadosExecutante' => [
                        'ans:contratadoExecutante' => [
                            'ans:codigoPrestadorNaOperadora' => $providerOperatorNumber,
                            'ans:nomeContratado' => $providerName,
                        ],
                        'ans:CNES' => $providerCNES
                    ],
                    'ans:dadosAtendimento' => [
                        'ans:tipoAtendimento' => $typeTreatment,
                        'ans:indicacaoAcidente' => $accidentIndication
                    ],
                    'ans:procedimentosExecutados' => [],
                    'ans:observacao' => $observation,
                    'ans:valorTotal' => [
                        'ans:valorTotalGeral' => $guide->total
                    ]
                ];
                if (empty($guide->request_date)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosSolicitacao']['ans:dataSolicitacao']);
                }
                if (empty($guide->clinical_indication)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosSolicitacao']['ans:indicacaoClinica']);
                }
                if (empty($guide->main_number)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:cabecalhoGuia']['ans:guiaPrincipal']);
                }
                if (empty($guide->guide_operator_number)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:numeroGuiaOperadora']);
                }
                if (empty($guide->password)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:senha']);
                }
                if (empty($guide->password_expiration)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosAutorizacao']['ans:dataValidadeSenha']);
                }
                if (empty($guide->patient->cns)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:dadosBeneficiario']['ans:numeroCNS']);
                }
                if (empty($guide->observation)) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:observacao']);
                }
                if ($guide->procedures->isEmpty()) {
                    unset($array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:procedimentosExecutados']);
                } else {
                    $sequencialItem = 1;
                    foreach ($guide->procedures as $procedure) {
                        $executionDate = Str::of(Str::ascii($procedure->guide_procedure->execution_date))->upper()->trim();
                        $number = Str::of(Str::ascii($procedure->number))->upper()->trim();
                        $description = Str::of(Str::ascii($procedure->description))->upper()->trim();
                        $hash .= $sequencialItem . $executionDate . "22" . $number . $description . $procedure->guide_procedure->permission_amount
                            . $procedure->guide_procedure->reduction_factor . $procedure->guide_procedure->unity_price
                            . $procedure->guide_procedure->permission_amount * $procedure->guide_procedure->unity_price;
                        $array['ans:prestadorParaOperadora']['ans:loteGuias']['ans:guiasTISS']['ans:guiaSP-SADT'][$cont]['ans:procedimentosExecutados']['ans:procedimentoExecutado'][] = [
                            'ans:sequencialItem' => $sequencialItem,
                            'ans:dataExecucao' => $executionDate,
                            'ans:procedimento' => [
                                'ans:codigoTabela' => '22',
                                'ans:codigoProcedimento' => $number,
                                'ans:descricaoProcedimento' => $description
                            ],
                            'ans:quantidadeExecutada' => $procedure->guide_procedure->permission_amount,
                            'ans:reducaoAcrescimo' => $procedure->guide_procedure->reduction_factor,
                            'ans:valorUnitario' => $procedure->guide_procedure->unity_price,
                            'ans:valorTotal' => $procedure->guide_procedure->permission_amount * $procedure->guide_procedure->unity_price
                        ];
                        $sequencialItem++;
                    }
                }
                $cont++;
                $hash .= $observation . $guide->total;
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
            $filename = $lotNumber . '_' . $array['ans:epilogo']['ans:hash'] . '.xml';
            Storage::makeDirectory($path);
            if (Storage::put($path . $filename, $result)) {
                return $this->message->success()->setData(['url' => Storage::url($path . $filename)])->getResponse();
            }
            return $this->message->error()
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Lote não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }
}

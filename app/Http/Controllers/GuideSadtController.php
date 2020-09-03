<?php

namespace App\Http\Controllers;

use App\GuideSadt;
use App\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class GuideSadtController extends Controller
{
    protected $requestData = [
        'lot_id',
        'doctor_id',
        'provider_id',
        'patient_id',
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
        'total',
        'observation',
        'procedures'
    ];

    protected $rules = [
        'lot_id' => 'required|exists:lots,id',
        'doctor_id' => 'required|exists:doctors,id',
        'provider_id' => 'required|exists:providers,id',
        'patient_id' => 'required|exists:patients,id',
        'provider_number' => 'required',
        'main_number' => 'nullable',
        'permission_date' => 'required|date_format:Y-m-d',
        'password' => 'nullable',
        'password_expiration' => 'nullable|date_format:Y-m-d',
        'guide_operator_number' => 'nullable',
        'rn' => 'required|in:N,S',
        'character_treatment' => 'required|in:1,2',
        'request_date' => 'nullable|date_format:Y-m-d',
        'clinical_indication' => 'nullable|max:150',
        'type_treatment' => 'required|in:01,02,03,04,05,06,07,08,09,10,11,13,14,15,16,17,18,19,20,21,22',
        'accident_indication' => 'required|in:0,1,2,9',
        'total' => 'required',
        'observation' => 'nullable|max:150',
        'procedures' => 'required|array'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = GuideSadt::with(['lot', 'lot.operator' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('lot.operator', function ($q) use ($operator, $searchValue) {
            $q->where('lots.operator_id', !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            $q->whereNull('lots.closed_at');
            $q->where(function ($q2) use ($searchValue) {
                $q2->where("lots.number", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.provider_number", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.password", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.total", "LIKE", "%$searchValue%");
            });
        })->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(Request $request, int $id)
    {
        try {
            $guide = GuideSadt::with([
                'lot',
                'lot.operator',
                'patient.operators',
                'doctor.operators',
                'provider.operators',
                'procedures'
            ])->findOrFail($id);
            return $this->message->info()->setData($guide->toArray())->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only($this->requestData), $this->rules);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $guide = GuideSadt::create($request->only($this->requestData));
            $guide->procedures()->sync($this->requestProcedures($request->procedures));
            return $this->message->success('Guia' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->setData([
                    $e->getMessage()
                ])
                ->getResponse();
        }
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->only($this->requestData), $this->rules);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $guide = GuideSadt::findOrFail($id);
            $guide->update($request->only($this->requestData));
            $guide->procedures()->sync($this->requestProcedures($request->procedures));
            return $this->message->success('Guia' . config('constants.messages.success.updated'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Guia não encontrada')->getResponse();
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
            $guide = GuideSadt::findOrFail($id);
            $guide->delete();
            return $this->message->success('Guia' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Guia não encontrada')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    protected function requestProcedures($requestProcedures)
    {
        $procedures = [];
        foreach ($requestProcedures as $procedure) {
            $procedures[$procedure['id']] = [
                'execution_date' => $procedure['guide_procedure']['execution_date'],
                'request_amount' => $procedure['guide_procedure']['request_amount'],
                'permission_amount' => $procedure['guide_procedure']['permission_amount'],
                'unity_price' => $procedure['guide_procedure']['unity_price']
            ];
        }
        return $procedures;
    }
}

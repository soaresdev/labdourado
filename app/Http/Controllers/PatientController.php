<?php

namespace App\Http\Controllers;

use App\Operator;
use App\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Patient::whereHas('operators', function ($q) use ($operator, $searchValue) {
            $q->where('patient_operators.operator_id', !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
            $q->where(function($q2) use ($searchValue) {
                $q2->where("patient_operators.wallet_number", "LIKE", "%$searchValue%")
                    ->orWhere("patients.id", "LIKE", "%$searchValue%")
                    ->orWhere("patients.name", "LIKE", "%$searchValue%")
                    ->orWhere("patients.cns", "LIKE", "%$searchValue%");
            });
        });
        $query->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only([
            'name',
            'operators',
            'cns'
        ]), [
            'name' => 'required',
            'operators' => 'required|array',
            'cns' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $patient = Patient::create([
                'name' => $validator->validated()['name'],
                'cns' => $validator->validated()['cns']
            ]);
            $operators = [];
            foreach($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'wallet_number' => $operator['wallet_number'],
                    'wallet_expiration' => $operator['wallet_expiration'],
                ];
            }
            $patient->operators()->sync($operators);
            return $this->message->success('Paciente' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData([$patient])
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->only([
            'name',
            'operators',
            'cns'
        ]), [
            'name' => 'required',
            'operators' => 'required|array',
            'cns' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $patient = Patient::findOrFail($id);
            $patient->update([
                'name' => $validator->validated()['name'],
                'cns' => $validator->validated()['cns']
            ]);
            $operators = [];
            foreach($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'wallet_number' => $operator['wallet_number'],
                    'wallet_expiration' => $operator['wallet_expiration'],
                ];
            }
            $patient->operators()->sync($operators);
            return $this->message->success('Paciente' . config('constants.messages.success.updated'))
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
            $patient = Patient::findOrFail($id);
            $patient->delete();
            return $this->message->success('Paciente' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}

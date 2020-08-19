<?php

namespace App\Http\Controllers;

use App\Doctor;
use App\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');

        $operator = $request->input('operator');
        $query = Doctor::whereHas('operators', function ($q) use ($operator, $searchValue) {
            $q->where('doctor_operators.operator_id', !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
            $q->where(function($q2) use ($searchValue) {
                $q2->where("doctor_operators.doctor_operator_number", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.id", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.name", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.advice_number", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.cbo", "LIKE", "%$searchValue%");
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
            'cp',
            'advice_number',
            'uf',
            'cbo',
            'operators',
        ]), [
            'name' => 'required',
            'cp' => 'required|in:01,02,03,04,05,06,07,08,09,10',
            'advice_number' => 'required',
            'uf' => 'required|in:11,12,13,14,15,16,17,21,22,23,24,25,26,27,28,29,31,32,33,35,41,42,43,50,51,52,53,98',
            'cbo' => 'required',
            'operators' => 'required|array'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $doctor = Doctor::create([
                'name' => $validator->validated()['name'],
                'cp' => $validator->validated()['cp'],
                'advice_number' => $validator->validated()['advice_number'],
                'uf' => $validator->validated()['uf'],
                'cbo' => $validator->validated()['cbo'],
            ]);
            $operators = [];
            foreach($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'doctor_operator_number' => $operator['doctor_operator_number']
                ];
            }
            $doctor->operators()->sync($operators);
            return $this->message->success('Médico' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData([$doctor])
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
            'cp',
            'advice_number',
            'uf',
            'cbo',
            'operators'
        ]), [
            'name' => 'required',
            'cp' => 'required|in:01,02,03,04,05,06,07,08,09,10',
            'advice_number' => 'required',
            'uf' => 'required|in:11,12,13,14,15,16,17,21,22,23,24,25,26,27,28,29,31,32,33,35,41,42,43,50,51,52,53,98',
            'cbo' => 'required',
            'operators' => 'required|array'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $doctor = Doctor::findOrFail($id);
            $doctor->update([
                'name' => $validator->validated()['name'],
                'cp' => $validator->validated()['cp'],
                'advice_number' => $validator->validated()['advice_number'],
                'uf' => $validator->validated()['uf'],
                'cbo' => $validator->validated()['cbo'],
            ]);
            $operators = [];
            foreach($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'doctor_operator_number' => $operator['doctor_operator_number']
                ];
            }
            $doctor->operators()->sync($operators);
            return $this->message->success('Médico' . config('constants.messages.success.updated'))
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
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();
            return $this->message->success('Médico' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}

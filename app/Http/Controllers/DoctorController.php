<?php

namespace App\Http\Controllers;

use App\Doctor;
use App\Operator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class DoctorController extends Controller
{
    protected $fillable = [
        'name',
        'cp',
        'advice_number',
        'uf',
        'cbo',
        'operators'
    ];

    protected $rulesCreate = [
        'name' => 'required|max:70',
        'cp' => 'required|in:01,02,03,04,05,06,07,08,09,10',
        'advice_number' => 'required',
        'uf' => 'required|in:11,12,13,14,15,16,17,21,22,23,24,25,26,27,28,29,31,32,33,35,41,42,43,50,51,52,53,98',
        'cbo' => 'required',
        'operators' => 'required|array'
    ];

    protected $rulesUpdate = [
        'name' => 'required|max:70',
        'cp' => 'required|in:01,02,03,04,05,06,07,08,09,10',
        'advice_number' => 'required',
        'uf' => 'required|in:11,12,13,14,15,16,17,21,22,23,24,25,26,27,28,29,31,32,33,35,41,42,43,50,51,52,53,98',
        'cbo' => 'required',
        'operators' => 'required|array'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Doctor::with(['operators' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('operators', function ($sql2) use ($operator, $searchValue, $orderBy, $sortBy) {
            $sql2->where("doctor_operators.operator_id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            $sql2->where(function ($sql3) use ($searchValue) {
                $sql3->where("doctor_operators.doctor_operator_number", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.name", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.advice_number", "LIKE", "%$searchValue%")
                    ->orWhere("doctors.cbo", "LIKE", "%$searchValue%");
            });
        })->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(int $id)
    {
        try {
            $doctor = Doctor::with('operators')->findOrFail($id);
            return $this->message->info()->setData($doctor->operators->all())->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Médico não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function store(Request $request)
    {
        $data = $request->only($this->fillable);
        $validator = Validator::make($data, $this->rulesCreate);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $doctor = Doctor::with('operators')->create($data);
            $doctor->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Médico' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData(Doctor::with(['operators' => function ($sql) use ($data) {
                    $sql->where("operators.id", $data['operators'][0]['id']);
                }])->findOrFail($doctor->id)->toArray())
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->setErrors([$e->getMessage()])
                ->getResponse();
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
            $doctor = Doctor::with('operators')->findOrFail($id);
            $doctor->update($data);
            $doctor->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Médico' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Médico não encontrado')->getResponse();
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
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();
            return $this->message->success('Médico' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Médico não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    protected function requestOperators($requestOperators)
    {
        $operators = [];
        foreach ($requestOperators as $operator) {
            $operators[$operator['id']] = [
                'doctor_operator_number' => $operator['doctor_operator']['doctor_operator_number']
            ];
        }
        return $operators;
    }
}

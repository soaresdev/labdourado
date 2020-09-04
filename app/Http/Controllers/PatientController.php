<?php

namespace App\Http\Controllers;

use App\Operator;
use App\Patient;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class PatientController extends Controller
{
    protected $fillable = [
        'name',
        'cns',
        'operators'
    ];

    protected $rulesCreate = [
        'name' => 'required|max:70',
        'cns' => 'nullable|max:15',
        'operators' => 'required|array'
    ];

    protected $rulesUpdate = [
        'name' => 'required|max:70',
        'cns' => 'nullable|max:15',
        'operators' => 'required|array'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Patient::with(['operators' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('operators', function ($sql2) use ($operator, $searchValue, $orderBy, $sortBy) {
            $sql2->where("patient_operators.operator_id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            $sql2->where(function ($sql3) use ($searchValue) {
                $sql3->where("patient_operators.wallet_number", "LIKE", "%$searchValue%")
                    ->orWhere("patients.name", "LIKE", "%$searchValue%")
                    ->orWhere("patients.cns", "LIKE", "%$searchValue%");
            });
        })->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(int $id)
    {
        try {
            $patient = Patient::with('operators')->findOrFail($id);
            return $this->message->info()->setData($patient->operators->all())->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Paciente não encontrado')->getResponse();
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
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $patient = Patient::with('operators')->create($data);
            $patient->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Paciente' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData(Patient::with(['operators' => function ($sql) use ($data) {
                    $sql->where("operators.id", $data['operators'][0]['id']);
                }])->findOrFail($patient->id)->toArray())
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
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
            $patient = Patient::with('operators')->findOrFail($id);
            $patient->update($data);
            $patient->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Paciente' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Paciente não encontrado')->getResponse();
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
            $patient = Patient::findOrFail($id);
            $patient->delete();
            return $this->message->success('Paciente' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Paciente não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function export()
    {
        $data = Patient::with('operators:id,name,ans')->get();
        $moment = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis');
        $filename = "pacientes_$moment.pdf";
        view()->share('patients', $data);
        $pdf = SnappyPdf::loadView('exports.patients', $data);
        return $pdf->download($filename);
    }

    protected function requestOperators($requestOperators)
    {
        $operators = [];
        foreach ($requestOperators as $operator) {
            $operators[$operator['id']] = [
                'wallet_number' => $operator['patient_operator']['wallet_number'],
                'wallet_expiration' => $operator['patient_operator']['wallet_expiration']
            ];
        }
        return $operators;
    }
}

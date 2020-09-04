<?php

namespace App\Http\Controllers;

use App\Operator;
use App\Procedure;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class ProcedureController extends Controller
{
    protected $fillable = [
        'number',
        'description',
        'operators'
    ];

    protected $rulesCreate = [
        'number' => 'required|max:10',
        'description' => 'nullable|max:150',
        'operators' => 'required|array'
    ];

    protected $rulesUpdate = [
        'number' => 'required|max:10',
        'description' => 'nullable|max:150',
        'operators' => 'required|array'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Procedure::with(['operators' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('operators', function ($sql2) use ($operator, $searchValue, $orderBy, $sortBy) {
            $sql2->where("procedure_operators.operator_id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            $sql2->where(function ($sql3) use ($searchValue) {
                $sql3->where("procedure_operators.price", "LIKE", "%$searchValue%")
                    ->orWhere("procedures.number", "LIKE", "%$searchValue%")
                    ->orWhere("procedures.description", "LIKE", "%$searchValue%");
            });
        })->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(int $id)
    {
        try {
            $procedure = Procedure::with('operators')->findOrFail($id);
            return $this->message->info()->setData($procedure->operators->all())->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Procedimento não encontrado')->getResponse();
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
            $procedure = Procedure::with('operators')->create($data);
            $procedure->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Procedimento' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData($procedure->toArray())
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Procedimento não encontrado')->getResponse();
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
            $procedure = Procedure::with('operators')->findOrFail($id);
            $procedure->update($data);
            $procedure->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Procedimento' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Procedimento não encontrado')->getResponse();
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
            $procedure = Procedure::findOrFail($id);
            $procedure->delete();
            return $this->message->success('Procedimento' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Procedimento não encontrado')->getResponse();
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
                'price' => $operator['procedure_operator']['price']
            ];
        }
        return $operators;
    }
}

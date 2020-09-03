<?php

namespace App\Http\Controllers;

use App\Operator;
use App\Provider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class ProviderController extends Controller
{
    protected $fillable = [
        'name',
        'cnes',
        'operators'
    ];

    protected $rulesCreate = [
        'name' => 'required|max:70',
        'cnes' => 'nullable|max:7',
        'operators' => 'required|array'
    ];

    protected $rulesUpdate = [
        'name' => 'required|max:70',
        'cnes' => 'nullable|max:7',
        'operators' => 'required|array'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Provider::with(['operators' => function ($sql) use ($operator, $sortBy, $orderBy) {
            $sql->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
        }])->whereHas('operators', function ($sql2) use ($operator, $searchValue, $orderBy, $sortBy) {
            $sql2->where("provider_operators.operator_id", !empty($operator) ? $operator : Operator::all(['id', 'name', 'ans'])->first()->id);
            $sql2->where(function ($sql3) use ($searchValue) {
                $sql3->where("provider_operators.provider_operator_number", "LIKE", "%$searchValue%")
                    ->orWhere("providers.name", "LIKE", "%$searchValue%")
                    ->orWhere("providers.cnes", "LIKE", "%$searchValue%");
            });
        })->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function indexData(Request $request)
    {
        $operator = $request->input('operator');
        return $this->message->info()->setData(Provider::whereHas('operators', function ($query) use ($operator) {
            $query->where('provider_operators.operator_id', $operator);
        })->get()->all())->getResponse();
    }

    public function show(int $id)
    {
        try {
            $provider = Provider::with('operators')->findOrFail($id);
            return $this->message->info()->setData($provider->operators->all())->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Prestador não encontrado')->getResponse();
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
            $provider = Provider::with('operators')->create($data);
            $provider->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Prestador' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData($provider->toArray())
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()->setErrors([
                $e->getMessage()
            ])->getResponse();
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
            $provider = Provider::with('operators')->findOrFail($id);
            $provider->update($data);
            $provider->operators()->sync($this->requestOperators($data['operators']));
            return $this->message->success('Prestador' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Prestador não encontrado')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function delete(int $id)
    {
        try {
            $provider = Provider::findOrFail($id);
            $provider->delete();
            return $this->message->success('Prestador' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Prestador não encontrado')->getResponse();
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
                'provider_operator_number' => $operator['provider_operator']['provider_operator_number']
            ];
        }
        return $operators;
    }
}

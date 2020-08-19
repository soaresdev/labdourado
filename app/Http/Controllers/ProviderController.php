<?php

namespace App\Http\Controllers;

use App\Operator;
use App\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');

        $operator = $request->input('operator');
        $query = Provider::whereHas('operators', function ($q) use ($operator, $searchValue) {
            $q->where('provider_operators.operator_id', !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
            $q->where(function($q2) use ($searchValue) {
                $q2->where("provider_operators.provider_operator_number", "LIKE", "%$searchValue%")
                    ->orWhere("providers.id", "LIKE", "%$searchValue%")
                    ->orWhere("providers.name", "LIKE", "%$searchValue%")
                    ->orWhere("providers.cnes", "LIKE", "%$searchValue%");
            });
        });
        $query->orderBy($sortBy, $orderBy);
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->only([
            'name',
            'cnes',
            'operators'
        ]), [
            'name' => 'required',
            'cnes' => 'nullable',
            'operators' => 'required|array'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $provider = Provider::create([
                'name' => $validator->validated()['name'],
                'cnes' => $validator->validated()['cnes']
            ]);
            $operators = [];
            foreach ($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'provider_operator_number' => $operator['provider_operator_number']
                ];
            }
            $provider->operators()->sync($operators);
            return $this->message->success('Prestador' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData([$provider])
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
            'cnes',
            'operators'
        ]), [
            'name' => 'required',
            'cnes' => 'nullable',
            'operators' => 'required|array'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $provider = Provider::findOrFail($id);
            $provider->update([
                'name' => $validator->validated()['name'],
                'cnes' => $validator->validated()['cnes']
            ]);
            $operators = [];
            foreach ($validator->validated()['operators'] as $operator) {
                $operators[$operator['operator_id']] = [
                    'provider_operator_number' => $operator['provider_operator_number']
                ];
            }
            $provider->operators()->sync($operators);
            return $this->message->success('Prestador' . config('constants.messages.success.updated'))
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
            $provider = Provider::findOrFail($id);
            $provider->delete();
            return $this->message->success('Prestador' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}

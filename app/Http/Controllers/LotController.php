<?php

namespace App\Http\Controllers;

use App\Lot;
use App\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $operator = $request->input('operator');
        $query = Lot::eloquentQuery($sortBy, $orderBy, $searchValue, [
            "operators"
        ]);
        $query->withCount('guides');
        $query->where("operators.id", !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function indexData(Request $request)
    {
        return $this->message->info()->setData(Lot::with([
            'operators',
            'operators.doctors',
            'operators.patients',
            'operators.providers',
        ])->where('lots.closed_at', null)->get()->all())->getResponse();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only([
            'number',
            'operators'
        ]), [
            'number' => 'required',
            'operators' => 'required|array'
        ]);
        if ($validator->fails()) {
            return $this->message->error(config('constants.messages.error.validation'))
                ->setStatus(422)
                ->setErrors($validator->errors()->all())
                ->getResponse();
        }
        try {
            $lot = Lot::create([
                'number' => $validator->validated()['number']
            ]);
            $operators = [];
            foreach ($validator->validated()['operators'] as $operator) {
                array_push($operators, $operator['operator_id']);
            }
            $lot->operators()->sync($operators);
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
            'operators',
            'closed_at'
        ]), [
            'number' => 'required',
            'operators' => 'required|array',
            'closed_at' => 'nullable'
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
                'number' => $validator->validated()['number'],
                'closed_at' => !empty($validator->validated()['closed_at']) ? $validator->validated()['closed_at'] : null
            ]);
            $operators = [];
            foreach ($validator->validated()['operators'] as $operator) {
                array_push($operators, $operator['operator_id']);
            }
            $lot->operators()->sync($operators);
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
}

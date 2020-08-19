<?php

namespace App\Http\Controllers;

use App\GuideSadt;
use App\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class GuideSadtController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');

        $operator = $request->input('operator');

        $query = GuideSadt::whereHas('lot.operator', function ($q) use ($operator, $searchValue) {
            $q->where('lots.operator_id', !empty($operator) ? $operator : Operator::all(['id', 'name'])->first()->id);
            $q->whereNull('lots.closed_at');
            $q->where(function($q2) use ($searchValue) {
                $q2->where("lots.number", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.provider_number", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.password", "LIKE", "%$searchValue%")
                    ->orWhere("guide_sadts.guide_operator_number", "LIKE", "%$searchValue%");
            });
        });
        $query->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(Request $request, int $id)
    {
        try{
            $guide = GuideSadt::findOrFail($id);
            return $this->message->info()->setData([
                $guide
            ])->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function store(Request $request)
    {
        try {
            GuideSadt::create($request->all());
            return $this->message->success('Guia' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function update(Request $request, int $id) {
        try {
            $guide = GuideSadt::findOrFail($id);
            $guide->update($request->all());
            return $this->message->success('Guia' . config('constants.messages.success.updated'))
                ->setStatus(201)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }

    public function delete(Request $request, int $id) {
        try {
            $guide = GuideSadt::findOrFail($id);
            $guide->delete();
            return $this->message->success('Guia' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            return $this->message->error()
                ->getResponse();
        }
    }
}

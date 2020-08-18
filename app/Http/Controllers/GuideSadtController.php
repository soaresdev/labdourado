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
        $operator = $request->input('operator');
        $query = GuideSadt::with([
            'lot.operators',
            'patient',
            'doctor',
            'provider'
        ]);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show(Request $request, int $id)
    {

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

    }

    public function delete(Request $request, int $id) {

    }
}

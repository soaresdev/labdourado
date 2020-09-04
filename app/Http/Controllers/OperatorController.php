<?php

namespace App\Http\Controllers;

use App\Operator;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class OperatorController extends Controller
{
    protected $fillable = [
        'name',
        'ans'
    ];

    protected $rulesCreate = [
        'name' => 'required|max:70',
        'ans' => 'required|max:6'
    ];

    protected $rulesUpdate = [
        'name' => 'required|max:70',
        'ans' => 'required|max:6'
    ];

    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $query = Operator::where("operators.id", "LIKE", "%$searchValue%")
            ->orWhere("operators.name", "LIKE", "%$searchValue%")
            ->orWhere("operators.ans", "LIKE", "%$searchValue%")
            ->orderBy($sortBy, $orderBy);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function indexData()
    {
        return $this->message->info()->setData(Operator::all(['id', 'name', 'ans'])->all())->getResponse();
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
            $operator = Operator::create($data);
            return $this->message->success('Operadora' . config('constants.messages.success.created'))
                ->setStatus(201)
                ->setData($operator->toArray())
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
            $operator = Operator::findOrFail($id);
            $operator->update($data);
            return $this->message->success('Operadora' . config('constants.messages.success.updated'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Operadora não encontrada')->getResponse();
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
            $provider = Operator::findOrFail($id);
            $provider->delete();
            return $this->message->success('Operadora' . config('constants.messages.success.deleted'))
                ->setStatus(200)
                ->getResponse();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->message->error('Operadora não encontrada')->getResponse();
            } else {
                return $this->message->error()->setErrors([
                    $e->getMessage()
                ])->getResponse();
            }
        }
    }

    public function export()
    {
        $data = Operator::with('lots')->withCount([
            'lots',
            'doctors',
            'providers',
            'patients',
            'procedures'
        ])->get();
        $moment = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis');
        $filename = "operadoras_$moment.pdf";
        view()->share('operators', $data);
        $pdf = SnappyPdf::loadView('exports.operators', $data);
        return $pdf->download($filename);
    }
}

<?php

namespace App\Http\Controllers;

use App\Procedure;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function indexData(Request $request)
    {
        return $this->message->info()->setData(Procedure::all(['id', 'table', 'number', 'description'])->all())->getResponse();
    }
}

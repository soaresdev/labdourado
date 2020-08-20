<?php

namespace App\Imports;

use App\Procedure;
use Maatwebsite\Excel\Concerns\ToModel;

class ProceduresImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Procedure([
            'number' => $row[0],
            'description' => $row[1]
        ]);
    }
}

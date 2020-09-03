<?php

namespace App\Imports;

use App\Operator;
use App\Procedure;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ProceduresImport implements OnEachRow
{
    /**
     * @param Row $row
     *
     */
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $procedure = Procedure::create([
            'number' => $row[0],
            'description' => $row[1]
        ]);
        $agros = Operator::find(1);
        $agros->procedures()->save($procedure, ['price' => str_replace(',', '.', $row[2])]);
    }
}

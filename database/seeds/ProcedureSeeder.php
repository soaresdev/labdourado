<?php

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProceduresImport;
use Illuminate\Support\Facades\Storage;

class ProcedureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new ProceduresImport, storage_path('procedures-agros.xlsx'));
    }
}

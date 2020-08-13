<?php

use App\Operator;
use App\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maria = Patient::create([
            'name' => 'Maria Amália de Assis Alves'
        ]);
        $agros = Operator::find(1);
        $maria->operators()->sync([$agros->id => ['wallet_number' => '030953031', 'wallet_expiration' => '9999-12-31']]);
    }
}

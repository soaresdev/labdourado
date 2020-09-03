<?php

use App\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agros = Operator::create([
            'name' => 'Agros',
            'ans' => '368920',
        ]);
        $teste = Operator::create([
            'name' => 'Teste',
            'ans' => '123456'
        ]);
        //Prestadora
        $lab = \App\Provider::create([
            'name' => 'Laboratório de Análises Clínicas Dourado LTDA',
            'cnes' => '9629130'
        ]);
        $agros->providers()->save($lab, ['provider_operator_number' => '3392']);
        $teste->providers()->save($lab, ['provider_operator_number' => '1234']);
        //Paciente
        $maria = \App\Patient::create([
            'name' => 'Maria Amália de Assis Alves'
        ]);
        $agros->patients()->save($maria, [
            'wallet_number' => '030953031',
            'wallet_expiration' => '9999-12-31'
        ]);
        //Médico
        $marcelo = \App\Doctor::create([
            'name' => 'Marcelo Mageste Rodrigues',
            'cp' => '06',
            'advice_number' => '58822',
            'uf' => '31',
            'cbo' => '225109'
        ]);
        $agros->doctors()->save($marcelo, [
            'doctor_operator_number' => '3311'
        ]);
        //Lot teste
        $agros->lots()->create([
            'number' => '1'
        ]);
    }
}

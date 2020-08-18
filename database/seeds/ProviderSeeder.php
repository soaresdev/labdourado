<?php

use App\Provider;
use App\Operator;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lab = Provider::create([
            'name' => 'Laboratório de Análises Clínicas Dourado LTDA',
            'cnes' => '9629130'
        ]);
        $agros = Operator::find(1);
        $lab->operators()->sync([$agros->id => [
            'provider_operator_number' => '3392'
        ]]);
    }
}

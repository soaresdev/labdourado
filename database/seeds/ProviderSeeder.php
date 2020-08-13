<?php

use App\Provider;
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
        Provider::create([
            'name' => 'Laboratório de Análises Clínicas Dourado LTDA',
            'cnes' => '9629130'
        ]);
    }
}

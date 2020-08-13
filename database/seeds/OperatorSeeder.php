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
        Operator::create([
            'name' => 'Agros',
            'ans' => '368920',
        ]);
    }
}

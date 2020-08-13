<?php

use App\Doctor;
use App\Operator;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marcelo = Doctor::create([
            'name' => 'Marcelo Mageste Rodrigues',
            'cp' => '06',
            'advice_number' => '58822',
            'uf' => '31',
            'cbo' => '225109'
        ]);
        $agros = Operator::find(1);
        $marcelo->operators()->sync([$agros->id => [
            'doctor_operator_number' => '3311'
        ]]);
    }
}

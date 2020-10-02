<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GuideSadt;
use Faker\Generator as Faker;

$factory->define(GuideSadt::class, function (Faker $faker) {
    return [
        'provider_number' => $faker->numerify(),
        'permission_date' => $faker->date(),
        'password' => $faker->numerify(),
        'password_expiration' => $faker->date(),
        'rn' => 'N',
        'character_treatment' => '1',
        'request_date' => $faker->date(),
        'type_treatment' => '05',
        'accident_indication' => '9',
        'total' => 24.02,
        'lot_id' => 1,
        'patient_id' => 1,
        'doctor_id' => 1,
        'provider_id' => 1
    ];
});

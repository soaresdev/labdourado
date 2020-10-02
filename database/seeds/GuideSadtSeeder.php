<?php

use App\Procedure;
use Illuminate\Database\Seeder;

class GuideSadtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\GuideSadt::class, 62)->create()->each(function ($guide) {
            $guide->procedures()->sync([
                4 => [
                    'execution_date' => now()->format('Y-m-d'),
                    'request_amount' => 1,
                    'permission_amount' => 1,
                    'unity_price' => 24.02
                ]
            ]);
        });
    }
}

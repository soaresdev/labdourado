<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            OperatorSeeder::class,
            ProcedureSeeder::class,
            GuideSadtSeeder::class
        ]);

    }
}

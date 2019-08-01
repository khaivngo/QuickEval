<?php

use Illuminate\Database\Seeder;

class ExperimentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\ExperimentType::class, 1)->create();
    }
}

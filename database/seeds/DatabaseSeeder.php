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
        // $this->call(UsersTableSeeder::class);

        factory(App\User::class, 2)->create();

        // $factory->state(App\User::class, 'experiment_types', [
        //     'name' => 'Rank order',
        //     'name' => 'Paired comparison',
        //     'name' => 'Category judgement',
        //     'name' => 'Artifact marking',
        //     'name' => 'Triplet comparison'
        // ]);
        $user = factory(App\ExperimentType::class)->create([
            'name' => 'Rank order',
            'name' => 'Paired comparison',
            'name' => 'Category judgement',
            'name' => 'Artifact marking',
            'name' => 'Triplet comparison'
        ]);
    }
}

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

        // factory(App\User::class, 2)->create();

        DB::table('experiment_types')->insert([
            ['name' => 'Paired Comparison', 'description' => ''],
            ['name' => 'Rank Order', 'description' => ''],
            ['name' => 'Category Judgement', 'description' => ''],
            // ['name' => 'Artifact Marking', 'description' => ''],
            ['name' => 'Triplet Comparison', 'description' => ''],
        ]);
    }
}

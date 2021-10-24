<?php

namespace Database\Seeders;

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
            ['name' => 'Paired Comparison',  'slug' => 'paired',     'description' => ''],
            ['name' => 'Rank Order',         'slug' => 'rank-order', 'description' => ''],
            ['name' => 'Category Judgement', 'slug' => 'category',   'description' => ''],
            ['name' => 'Triplet Comparison', 'slug' => 'triplet',    'description' => ''],
            ['name' => 'Magnitude Estimation','slug' => 'magnitude', 'description' => ''],
            // ['name' => 'Artifact Marking', 'description' => ''],
        ]);
    }
}

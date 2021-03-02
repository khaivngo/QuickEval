<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

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
            ['id' => 1, 'name' => 'Paired Comparison',  'slug' => 'paired',     'description' => ''],
            ['id' => 2, 'name' => 'Rank Order',         'slug' => 'rank-order', 'description' => ''],
            ['id' => 3, 'name' => 'Category Judgement', 'slug' => 'category',   'description' => ''],
            ['id' => 5, 'name' => 'Triplet Comparison', 'slug' => 'triplet',    'description' => ''],
            // ['name' => 'Artifact Marking', 'description' => ''],
        ]);
    }
}

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
            ['id' => 6, 'name' => 'Magnitude Estimation','slug' => 'magnitude', 'description' => ''],
            ['id' => 7, 'name' => 'Match Estimation',    'slug' => 'match',     'description' => ''],
            // ['name' => 'Artifact Marking', 'description' => ''],
        ]);

        // DB::table('experiments')->truncate();
        // DB::table('experiment_categories')->truncate();
        // DB::table('experiment_observer_metas')->truncate();
        // DB::table('experiment_queues')->truncate();
        // DB::table('experiment_results')->truncate();
        // DB::table('experiment_sequences')->truncate();
        // DB::table('instructions')->truncate();
        // DB::table('observer_metas')->truncate();
        // DB::table('picture_queues')->truncate();
        // DB::table('picture_sequences')->truncate();
        // DB::table('result_categories')->truncate();
        // DB::table('result_image_artifacts')->truncate();
        // DB::table('result_observer_metas')->truncate();
        // DB::table('result_pairs')->truncate();
        // DB::table('result_rank_orders')->truncate();
        // DB::table('result_triplets')->truncate();
    }
}

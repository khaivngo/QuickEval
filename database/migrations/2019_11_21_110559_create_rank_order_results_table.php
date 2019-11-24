<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRankOrderResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_order_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('experiment_result_id');

            $table->bigInteger('picture_set_id');
            $table->bigInteger('picture_id');
            $table->integer('ranking');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rank_order_results');
    }
}

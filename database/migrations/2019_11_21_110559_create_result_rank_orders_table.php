<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultRankOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_rank_orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('experiment_result_id');

            $table->bigInteger('picture_set_id');
            $table->bigInteger('picture_id');
            $table->integer('ranking');

            $table->integer('client_side_timer')->nullable();

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
        Schema::dropIfExists('result_rank_orders');
    }
}

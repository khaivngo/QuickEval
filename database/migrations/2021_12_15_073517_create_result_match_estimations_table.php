<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultMatchEstimationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_match_estimations', function (Blueprint $table) {
            $table->id();

            $table->integer('experiment_result_id')->nullable();

            $table->integer('picture_id_left');
            $table->integer('picture_id_original');
            $table->integer('magnitude_value');

            $table->tinyInteger('chose_none')->nullable();

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
        Schema::dropIfExists('result_match_estimations');
    }
}

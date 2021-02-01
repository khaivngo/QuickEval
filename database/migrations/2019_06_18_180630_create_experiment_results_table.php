<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('experiment_id');
            $table->bigInteger('user_id');

            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->integer('x')->nullable();
            $table->integer('y')->nullable();
            $table->integer('start_time')->nullable();
            $table->integer('end_time')->nullable();
            $table->tinyInteger('completed')->nullable();

            $table->string('vision')->nullable();
            $table->string('post_eval')->nullable();
            $table->string('degree')->nullable();

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
        Schema::dropIfExists('experiment_results');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperimentScientistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_scientists', function (Blueprint $table) {
            $table->id();

            $table->integer('experiment_id');
            $table->integer('user_id');

            $table->timestamps();

            // $table->foreign('experiment_id')->references('id')->on('experiments');
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiment_scientists');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentObserverMetaResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_observer_meta_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('experiment_id');
            $table->integer('user_id');
            $table->integer('observer_meta_id');

            $table->string('answer')->nullable();

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
        Schema::dropIfExists('experiment_observer_meta_results');
    }
}

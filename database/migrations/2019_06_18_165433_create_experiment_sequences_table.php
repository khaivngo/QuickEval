<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_sequences', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('picture_set')->nullable(); // can this be removed?
            $table->bigInteger('experiment_queue');
            $table->bigInteger('picture_queue');
            $table->bigInteger('instruction')->nullable();

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiment_sequences');
    }
}

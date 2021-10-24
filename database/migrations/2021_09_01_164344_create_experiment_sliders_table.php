<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperimentSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_sliders', function (Blueprint $table) {
            $table->id();

            $table->integer('experiment_id')->nullable();

            $table->integer('min_value');
            $table->integer('max_value');

            $table->string('min_label');
            $table->string('max_label');

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
        Schema::dropIfExists('experiment_sliders');
    }
}

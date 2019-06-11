<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title');
            $table->string('short_description');
            $table->text('long_description');

            $table->integer('user_id');
            $table->integer('experiment_type');

            $table->tinyInteger('is_public');
            $table->tinyInteger('allow_colour_blind');
            $table->tinyInteger('timer');

            $table->tinyInteger('allow_ties');
            $table->tinyInteger('show_original');
            $table->tinyInteger('same_pair');
            $table->tinyInteger('horizontal_flip');
            $table->tinyInteger('natural_lighting');

            $table->string('background_colour',     50)->nullable()->default(NULL);
            $table->string('monitor_distance',      50)->nullable()->default(NULL);
            $table->string('light_type',            50)->nullable()->default(NULL);
            $table->string('viewing_distance',      50)->nullable()->default(NULL);
            $table->string('screen_luminance',      50)->nullable()->default(NULL);
            $table->string('white_point',           50)->nullable()->default(NULL);
            $table->string('white_point_room',      50)->nullable()->default(NULL);
            $table->string('ambient_illumination',  50)->nullable()->default(NULL);
            $table->string('invite_hash', 100);

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
        Schema::dropIfExists('experiments');
    }
}

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

            $table->unsignedInteger('first_version_id')->nullable();
            $table->unsignedSmallInteger('version')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title');
            $table->integer('experiment_type_id');

            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();

            $table->integer('picture_sequence_algorithm')->nullable();
            $table->integer('delay')->default(200);

            $table->unsignedSmallInteger('stimuli_spacing')->default(15);

            $table->tinyInteger('is_public')->default(0);
            $table->tinyInteger('allow_colour_blind')->nullable();
            $table->tinyInteger('timer')->nullable();

            $table->tinyInteger('allow_ties')       ->nullable();
            $table->tinyInteger('show_original')    ->nullable();
            $table->tinyInteger('same_pair')        ->nullable();
            $table->tinyInteger('horizontal_flip')  ->nullable();
            $table->tinyInteger('natural_lighting') ->nullable();

            $table->string('background_colour',     50)->nullable();
            $table->string('monitor_distance',      50)->nullable();
            $table->string('light_type',            50)->nullable();
            $table->string('viewing_distance',      50)->nullable();
            $table->string('screen_luminance',      50)->nullable();
            $table->string('white_point',           50)->nullable();
            $table->string('white_point_room',      50)->nullable();
            $table->string('ambient_illumination',  50)->nullable();
            $table->string('invite_hash', 100)->default('');

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

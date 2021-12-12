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

            $table->bigInteger('picture_set_id')->nullable(); // can this be removed?
            $table->bigInteger('experiment_queue_id');
            // $table->foreign('experiment_queue_id')->references('id')->on('experiment_queues');
            // $table->foreignId('experiment_queue_id')->constrained()->onDelete('cascade');

            $table->bigInteger('picture_queue_id')->nullable(); # will be NULL if instruction_id is set
            $table->bigInteger('instruction_id')->nullable(); # will be NULL if picture_queue_id is set

            $table->tinyInteger('randomize')->nullable();
            $table->tinyInteger('randomize_group')->nullable();
            $table->tinyInteger('randomize_across')->nullable();
            $table->tinyInteger('original')->nullable();
            $table->tinyInteger('flipped')->nullable();
            $table->integer('hide_image_timer')->nullable();

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
        Schema::dropIfExists('experiment_sequences');
    }
}

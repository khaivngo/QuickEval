<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultImageArtifactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_image_artifacts', function (Blueprint $table) {
            $table->id();

            $table->integer('experiment_result_id');
            $table->integer('picture_id');
            // $table->integer('user_id');
            $table->longText('selected_area');
            // $table->integer('picture_sequence_id');
            $table->text('comment')->nullable();

            $table->integer('client_side_timer');

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
        Schema::dropIfExists('result_image_artifacts');
    }
}

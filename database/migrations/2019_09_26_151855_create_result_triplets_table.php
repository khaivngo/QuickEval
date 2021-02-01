<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultTripletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_triplets', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('experiment_result_id')->nullable();

            $table->string('category_id_left');
            $table->string('category_id_middle');
            $table->string('category_id_right');

            $table->integer('picture_id_left');
            $table->integer('picture_id_middle');
            $table->integer('picture_id_right');

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
        Schema::dropIfExists('result_triplets');
    }
}

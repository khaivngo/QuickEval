<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('experiment_result_id')->nullable();

            $table->integer('picture_id_left');
            $table->integer('category_id');

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
        Schema::dropIfExists('result_categories');
    }
}

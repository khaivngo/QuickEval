<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('user_id'); // no not need
            $table->integer('experiment_id'); // no not need
            $table->integer('experiment_result_id')->nullable();
            $table->integer('picture_order_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->tinyInteger('chose_none')->nullable();

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
        Schema::dropIfExists('results');
    }
}

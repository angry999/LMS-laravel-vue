<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitActivityProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_activity_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_progress_id')->unsigned();
            $table->integer('activity_number')->unsigned();
            $table->integer('last_score')->unsigned();
            $table->integer('best_score')->unsigned();
            $table->timestamps();
            $table->foreign('unit_progress_id')->references('id')->on('unit_progress')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_activity_progress');
    }
}

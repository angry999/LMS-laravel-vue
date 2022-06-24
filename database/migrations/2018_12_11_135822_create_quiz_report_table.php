<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_progress_id')->unsigned();
            $table->integer('correct')->unsigned();
            $table->integer('total')->unsigned();
            $table->text('results');
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
        Schema::dropIfExists('quiz_report');
    }
}

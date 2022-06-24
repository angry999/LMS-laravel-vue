<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentenceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reading_report_id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->integer('sentence_id')->unsigned();
            $table->integer('grade')->unsigned();
            $table->timestamps();
            $table->foreign('reading_report_id')->references('id')->on('reading_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sentence_reports');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadingReportFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reading_reports', function (Blueprint $table) {
            $table->integer('is_sync')->unsigned()->default(0)->after('average_score');
            $table->integer('practiced_sentences')->unsigned()->after('average_score');
        });
        
        Schema::table('quiz_report', function (Blueprint $table) {
            $table->integer('is_sync')->unsigned()->default(0)->after('results');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reading_reports', function (Blueprint $table) {
            $table->dropColumn('is_sync');
            $table->dropColumn('practiced_sentences');
        });
        
        Schema::table('quiz_report', function (Blueprint $table) {
            $table->dropColumn('is_sync');
        });
    }
}

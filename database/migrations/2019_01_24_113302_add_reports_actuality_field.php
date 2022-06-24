<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportsActualityField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reading_reports', function (Blueprint $table) {
            $table->integer('is_actual')->unsigned()->default(0)->after('is_sync');
        });
        
        Schema::table('quiz_report', function (Blueprint $table) {
            $table->integer('is_actual')->unsigned()->default(0)->after('is_sync');
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
            $table->dropColumn('is_actual');
        });
        
        Schema::table('quiz_report', function (Blueprint $table) {
            $table->dropColumn('is_actual');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuizFlagToUnitProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_progress', function (Blueprint $table) {
            $table->tinyInteger('has_quiz')->unsigned()->default(0)->after('best_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_progress', function (Blueprint $table) {
            $table->dropColumn('has_quiz');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChargesToContribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributions', function (Blueprint $table) {
           if (!Schema::hasColumn('contributions', 'charged_amount')) {
               $table->string('charged_amount')->default(0);
           }
            if (!Schema::hasColumn('contributions', 'charged_percentage')) {
               $table->string('charged_percentage')->default(0);
           }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributions', function (Blueprint $table) {
           if (Schema::hasColumn('contributions', 'charged_amount')) {
               $table->dropColumn('charged_amount');
           }
            if (Schema::hasColumn('contributions', 'charged_percentage')) {
               $table->dropColumn('charged_percentage');
           }
        });
    }
}

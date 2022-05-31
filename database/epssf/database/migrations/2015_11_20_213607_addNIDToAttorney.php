<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNIDToAttorney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attorney', function (Blueprint $table) {
           if (!Schema::hasColumn('attorney', 'nid')) {
               $table->string('nid')->nullable();
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
        Schema::table('attorney', function (Blueprint $table) {
            if (Schema::hasColumn('attorney', 'nid')) {
               $table->dropColumn('nid');
           }
        });
    }
}

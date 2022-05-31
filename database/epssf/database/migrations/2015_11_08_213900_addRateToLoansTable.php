<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateToLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('laons', 'rate') == false) {
                $table->decimal('rate',10,2);
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
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'rate')) {
                $table->dropColumn('rate');
            }
        });
    }
}

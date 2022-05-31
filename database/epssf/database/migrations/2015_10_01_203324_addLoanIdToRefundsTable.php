<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanIdToRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('refunds', function (Blueprint $table) {
             if (!Schema::hasColumn('refunds', 'loan_id')) {
                 $table->integer('loan_id');
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
       Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn("loan_id");
        });
    }
}

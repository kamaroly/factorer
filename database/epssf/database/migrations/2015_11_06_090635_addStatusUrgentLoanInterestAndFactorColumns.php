<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusUrgentLoanInterestAndFactorColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->decimal('urgent_loan_interests',10,2)->default(0);
            $table->decimal('factor',10,2)->default(0);
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
            $table->dropColumn('status');
            $table->dropColumn('urgent_loan_interests');
            $table->dropColumn('factor');
        });
    }
}

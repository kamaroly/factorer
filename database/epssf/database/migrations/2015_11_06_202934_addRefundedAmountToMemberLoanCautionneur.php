<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefundedAmountToMemberLoanCautionneur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_loan_cautionneurs', function (Blueprint $table) {
            $table->decimal('refunded_amount',10,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_loan_cautionneurs', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLoanCautionneurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_loan_cautionneurs', function($table){
            $table->increments('id');
            $table->integer('member_adhersion_id')->index();
            $table->integer('cautionneur_adhresion_id')->index();
            $table->decimal('amount',10,2);
            $table->string('transaction_id');
            $table->string('loan_id');
            $table->string('letter_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('member_loan_cautionneurs');
    }
}

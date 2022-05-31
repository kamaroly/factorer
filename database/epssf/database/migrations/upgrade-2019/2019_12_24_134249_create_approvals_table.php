<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('transactionid');
            $table->string('nature'); // Loan / Refund / Contribution...
            $table->string('type');   // loan_adjustment / refund_adjustment 
            $table->text('content');  // Json object of content to be approved
            $table->string('status'); // approved,rejcted,pending,in-progress
            $table->string('approvers'); // user of the approval
            $table->text('replaced_content')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('approvals');
    }
}

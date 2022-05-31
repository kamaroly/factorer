<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRegulationBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_regulations_backup', function (Blueprint $table) {
           $table->increments('id');
            $table->string('transactionid');
            $table->string('loan_contract');
            $table->integer('adhersion_id');
            $table->string('movement_nature');
            $table->string('operation_type');
            $table->timestamp('letter_date');
            $table->decimal('right_to_loan', 16, 2);
            $table->decimal('wished_amount', 16, 2);
            $table->decimal('loan_to_repay', 16, 2);
            $table->decimal('interests', 16, 2);
            $table->decimal('InteretsPU', 16, 2);
            $table->decimal('amount_received', 16, 2);
            $table->integer('tranches_number');
            $table->decimal('monthly_fees', 16, 2);
            $table->string('cheque_number');
            $table->string('bank_id');
            $table->string('security_type')->nullable(); // Type de caution
            $table->string('cautionneur1');
            $table->string('cautionneur2');
            $table->decimal('average_refund', 16, 2);
            $table->decimal('amount_refounded', 16, 2);
            $table->text('comment')->nullable(); // Libelle
            $table->string('special_loan_contract_number')->default(0);
            $table->integer('remaining_tranches')->default(0);
            $table->decimal('special_loan_tranches')->default(0);
            $table->decimal('special_loan_interests')->default(0);
            $table->decimal('special_loan_amount_to_receive')->default(0);
            $table->integer('user_id');
            $table->text('contract');
            $table->string('status')->default('pending');
            $table->decimal('urgent_loan_interests',10,2)->default(0);
            $table->decimal('factor',10,2)->default(0);
            $table->decimal('rate',10,2);
            $table->string('reason')->nullable();
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
        Schema::drop('loan_regulations_backup');
    }
}

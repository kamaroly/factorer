<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRefundsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('refunds', function (Blueprint $table) {
			$table->increments('id');
			$table->string('adhersion_id');
			$table->string('contract_number');
			$table->string('month');
			$table->decimal('amount', 16, 2);
			$table->string('tranches_number')->nullable();
			$table->string('transaction_id');
			$table->integer('member_id');
			$table->integer('user_id');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('refunds');
	}
}

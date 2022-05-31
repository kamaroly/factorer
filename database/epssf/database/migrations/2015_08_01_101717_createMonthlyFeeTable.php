<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMonthlyFeeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('member_monthly_fees_logs', function (Blueprint $table) {
			$table->increments('id');
			$table->decimal('old_monthly_fee', 10, 2);
			$table->decimal('new_monthly_fee', 10, 2);
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
		Schema::dropIfExists('member_monthly_fees_logs');
	}
}

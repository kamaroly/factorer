<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTransactionTypeAndPeriodInPostings extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('postings', function (Blueprint $table) {
			$table->string('transaction_type'); // This can be debit or Credit
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('postings', function (Blueprint $table) {
			$table->dropColumn('transaction_type');
		});
	}
}

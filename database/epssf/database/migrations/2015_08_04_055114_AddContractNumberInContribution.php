<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddContractNumberInContribution extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('contributions', function (Blueprint $table) {
			$table->string('contract_number');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('contributions', function (Blueprint $table) {
			$table->dropColumn('contract_number');
		});
	}
}

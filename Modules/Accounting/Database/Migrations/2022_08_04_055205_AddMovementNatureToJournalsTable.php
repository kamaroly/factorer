<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMovementNatureToJournalsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('journals', function (Blueprint $table) {
			$table->string('movement_nature');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('journals', function (Blueprint $table) {
			$table->dropColumn('movement_nature');
		});
	}
}

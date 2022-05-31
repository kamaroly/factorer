<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameDocumentId extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('contributions', function (Blueprint $table) {
			$table->dropColumn('document_id');

			$table->string('transactionid');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('contributions', function (Blueprint $table) {
			$table->dropColumn('transactionid');
			$table->string('document_id');
		});
	}
}

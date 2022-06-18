<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostingsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

        Schema::dropIfExists('postings');

		Schema::create('postings', function (Blueprint $table) {
			$table->increments('id');
			$table->string('transactionid');
			$table->integer('account_id');
			$table->integer('journal_id');
			$table->string('asset_type')->nullable();
			$table->decimal('amount', 10, 2);
			$table->integer('user_id');
			$table->string('account_period');
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
		Schema::dropIfExists('postings');
	}
}

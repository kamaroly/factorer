<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('leaves', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('days');
            $table->string('phone');
            $table->string('backup');
            $table->string('status');
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
        Schema::drop('leaves');
    }
}

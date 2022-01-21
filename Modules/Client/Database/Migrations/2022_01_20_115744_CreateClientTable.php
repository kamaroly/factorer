<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
   
   {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id','7');
            $table->string('first_name','125');
            $table->string('last_name','125');
            $table->string('company_name','125');
            $table->string('district','60');
            $table->string('province','60');
            $table->string('telephone','12');
            $table->string('bank','30');
            $table->string('account_number','100');
            $table->string('TIN','12');
            $table->string('description','255')->nullable();
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
        Schema::dropIfExists('clients');
    }
}

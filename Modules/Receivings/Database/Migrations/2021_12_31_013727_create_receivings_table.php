<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            
            $table->string('item_name');
            $table->string('item_sku');
            $table->string('grouping');
            $table->decimal('item_qty', 10, 2);
            $table->decimal('item_buying_price', 10, 2);
            $table->string('item_selling_price', 10, 2)->nullable();
            $table->timestamp('received_at');

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
        Schema::dropIfExists('receivings');
    }
}

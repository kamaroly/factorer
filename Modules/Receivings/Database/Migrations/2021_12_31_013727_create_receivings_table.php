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
            $table->decimal('item_qty', 10, 2);
            $table->string('item_type');
            $table->decimal('item_buying_price', 10, 2);
            $table->string('item_total', 10, 2)->nullable();
            $table->string('item_comment');     
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

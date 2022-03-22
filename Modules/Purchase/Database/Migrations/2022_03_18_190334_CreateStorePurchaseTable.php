<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
                     
            $table->string('item_name');
            $table->decimal('item_qty', 10, 2);
            $table->string('item_type');
            $table->string('item_mouvement');
            $table->string('item_comment'); 
            $table->string('item_status'); 
            $table->string('userid'); 
            $table->string('approved_by');
            $table->timestamp('initiated_at');
            $table->timestamp('approved_at');            
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
        Schema::dropIfExists('purchases');
    }
}

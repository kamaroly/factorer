<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMovementToAddcolmunPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
        
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
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
        });
    }
}

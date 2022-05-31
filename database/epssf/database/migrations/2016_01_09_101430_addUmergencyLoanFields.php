<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUmergencyLoanFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->boolean('is_umergency')->default(false);
            $table->decimal('emergency_refund',10,2)->default(0);
            $table->decimal('emergency_balance',10,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = ['is_umergency','emergency_refund','emergency_balance'];
            
            $table->dropColumn($columns);
        });
    }
}

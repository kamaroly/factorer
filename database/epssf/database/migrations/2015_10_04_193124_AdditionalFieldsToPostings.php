<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionalFieldsToPostings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postings', function($table){
            $table->string('wording')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('postings', function($table){
            $table->dropColumn('wording');
            $table->dropColumn('cheque_number');
            $table->dropColumn('bank');
        });

    }

}

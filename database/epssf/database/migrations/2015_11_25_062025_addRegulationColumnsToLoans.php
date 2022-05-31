<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegulationColumnsToLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'is_regulation')) {
                $table->boolean('is_regulation')->default(false);
            }

            if (!Schema::hasColumn('loans', 'regulation_type')) {
                $table->string('regulation_type')->nullable();
            }
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
            if (Schema::hasColumn('loans', 'is_regulation')) {
                $table->dropColumn('is_regulation');
            }

            if (Schema::hasColumn('loans', 'regulation_type')) {
                $table->dropColumn('regulation_type');
            }
        });
    }
}

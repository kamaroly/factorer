<?php

use Illuminate\Database\Seeder;

class LoanRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
	$loanRates = [
    	0=> [
			'start_month'=> 1,
			'end_month'	 => 12,
			'rate'		 => 3.4,
    	    ],
    	1=> [
			'start_month'=> 13,
			'end_month'	 => 24,
			'rate'		 => 3.6,
    	    ],
    	2=> [
			'start_month'=> 25,
			'end_month'	 => 36,
			'rate'		 => 4.1,
    	    ],
	    3=> [
			'start_month'=> 37,
			'end_month'	 => 48,
			'rate'		 => 4.3,
	    	],
	    4=> [
			'start_month'=> 49,
			'end_month'	 => 60,
			'rate'		 => 4.8,
	    	],
	     5=> [
			'start_month'=> 61,
			'end_month'	 => 72,
			'rate'		 => 5,
	    	],
    	];

    	DB::table('loan_rates')->truncate();

        DB::table('loan_rates')->insert($loanRates);
    }
}

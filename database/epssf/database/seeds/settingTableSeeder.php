<?php

use Illuminate\Database\Seeder;

class settingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	 
	 $settings = [
	  0 => [
	  	    'key'	=> 'loan.member.minimum.months',
	  	    'value' => 6,
	  	    'type'  => 'numeric',
	  	   ],
	  1 => [
	  	    'key'=> 'loan.give.ordinary.loan.2',
	  	    'value' => false,
	  	    'type'  => 'boolean',
	  	   ],
	  2 => [
	  	    'key'=> 'loan.maximum.cautionneurs',
	  	    'value' => 2,
	  	    'type'  => 'numeric',
	  	   ],
	  3 => [
	  	    'key'=> 'loan.maximum.installments',
	  	    'value' => 72,
	  	    'type'  => 'numeric',
	  	   ],
	  4 => [
	  	    'key'=> 'loan.wished.amount',
	  	    'value' => 2.5,
	  	    'type'  => 'numeric',
	  	   ],
      5 => [
	  	    'key'=> 'loan.allow.member.with.negative.right.to.loan',
	  	    'value' => 1,
	  	    'type'  => 'boolean',
	  	   ],
	  6 => [
	  	    'key'=> 'loan.administration.fee',
	  	    'value' => 2,
	  	    'type'  => 'percentage',
	  	   ],
	  7 => [
	  	    'key'=> 'special_loan.amount',
	  	    'value' => 100000,
	  	    'type'  => 'numeric',
	  	   ],
	  8 => [
	  	    'key'=> 'social_loan.amount',
	  	    'value' => 500000,
	  	    'type'  => 'numeric',
	  	   ],
	  9 => [
	  	    'key'=> 'emergency_loan.amount',
	  	    'value' => 210000,
	  	    'type'  => 'numeric',
	  	   ],
	  10 => [
	  	    'key'=> 'urgent.administration.fee',
	  	    'value' => 10,
	  	    'type'  => 'percentage',
	  	   ],
	  11 =>[
		  	'key' => 'loan.allow.one.ordinary.loan.only',
		   'value' => 1,
	  	   'type'  => 'boolean',
	  	   ],
      12 =>[
		  	'key' => 'general.done_by',
		   'value' => null,
	  	   'type'  => 'string',
	  	   ], 
	  13 =>[
		  	'key' => 'general.gerant',
		   'value' => 'Gera wa ceb',
	  	   'type'  => 'string',
	  	   ],
	  14 =>[
		  	'key' => 'general.president',
		   'value' => 'President wa ceb',
	  	   'type'  => 'string',
	  	   ],
	  15 =>[
		  	'key' => 'general.tresorien',
		   'value' => null,
	  	   'type'  => 'string',
	  	   ],
	  16 =>[
		  	'key' => 'general.controller',
		   'value' => null,
	  	   'type'  => 'string',
	  	   ],
	  17 =>[
		  	'key' => 'general.administrator',
		   'value' => null,
	  	   'type'  => 'string',
	  	   ],
	  18 =>[
		  	'key' => 'loan.emergency.installments',
		   'value' => 3,
	  	   'type'  => 'numeric',
	  	   ],   
	  18 =>[
		  	'key' => 'loan.emergency.rate',
		   'value' => 5,
	  	   'type'  => 'numeric',
	  	   ], 	   
	 ];

	 DB::table('settings')->truncate();

     DB::table('settings')->insert($settings);
    }
}

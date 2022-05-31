<?php

use Illuminate\Database\Seeder;

class NotificationCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
	  $notificatCategories =    [
								  0 =>  [
								    "name" => "loan.loan",
								    "text" => "{from.username} added loan that needs your review",
								  ],
								  1 =>  [
								    "name" => "leave.leave",
								    "text" => "{from.username} had requested leave the needs your approval",
								  ],
								  2 =>  [
								    "name" => "leave.approved",
								    "text" => "{from.username} has approved your leaveval",
								  ],
								  3 =>  [
								    "name" => "leave.rejected",
								    "text" => "{from.username} has rejected your leave",
								  ],
								  4 =>  [
								    "name" => "loan.approval",
								    "text" => "{from.username} added loan that needs your approval.",
								  ],
								  5 =>  [
								    "name" => "loan.request.received",
								    "text" => "{from.username} has received your loan request.",
								  ],
								  6 =>  [
								  	"name" => "loan.approved",
								  	"text" => "Your loan request has been approved",
								  ]
								  
								];
		 DB::table('notification_categories')->truncate();
		 DB::table('notification_categories')->insert($notificatCategories);

    }
}

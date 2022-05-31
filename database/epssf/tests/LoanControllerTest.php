<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoanControllerTest extends TestCase
{
	protected $controller;
	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('\Ceb\Http\Controllers\LoanController');
	}
 	
 	public function test_user_can_remove_cautionneur_from_a_loan()
 	{

 		$this->user();
        $method = $this->controller->removeCautionneur('cautionneur1');
        $this->assertEquals($indexMethod->getName(),'corrections.loan.find-loan');
 	}
}

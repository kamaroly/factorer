<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefundCorrectionControllerTest extends TestCase
{
	protected $controller;
	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('\Ceb\Http\Controllers\RefundCorrectionController');
	}

	// 1. Form for searching transactions
	// 2. If transaction is found, then load it in the session.
	// 3. If transactions exists then update them in the system.
    public function test_customer_can_see_transaction_load_form()
    {
        $controller = $this->controller->index();
        $this->assertEquals($controller->getName(),'corrections.refunds.index');
    }

    public function test_user_can_find_transaction_id()
    {
 		 $this->assertEquals($this->controller->show()->getName(),'corrections.refunds.index');
    }
}

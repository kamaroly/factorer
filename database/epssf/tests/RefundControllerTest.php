<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefundControllerTest extends TestCase
{
	private $controller;

	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('Ceb\Http\Controllers\RefundController');
	}

    public function testUserCanSetRefundTypeOfEmergency()
    {
         Input::merge(['refund_type' => 'refund_by_interets_annuels']);
         $this->controller->setRefundType();
         $this->assertSessionHas('refund_type','refund_by_interets_annuels');
    }

    public function testUserCanSetRefundLoanType()
    {
         Input::merge(['refund-loan-type' => 'NON_EMERGENCY_LOAN']);
         $this->controller->setRefundLoanType();
         $this->assertSessionHas('refund-loan-type','NON_EMERGENCY_LOAN');
    }
}

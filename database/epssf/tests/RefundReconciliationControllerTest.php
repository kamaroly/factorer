<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefundReconciliationControllerTest extends TestCase
{

	private $controller;

	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('Ceb\Http\Controllers\RefundReconciliationController');
	}

	public function test_index_method()
	{
		$this->assertEquals($this->controller->index()->getName(),'reconciliations.refunds.index');
	}
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationControllertTest extends TestCase
{
	protected $controller;

	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('Ceb\Http\Controllers\BankReconciliationController');
	}
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index()
    {
        $this->assertEquals($this->controller->index()->getName(),'reconciliations.banks.index');
    }

    public function test_reconcile_bank()
    {
        $this->assertEquals($this->controller->reconcileBank()->getName(),'reconciliations.banks.index');
    }
}

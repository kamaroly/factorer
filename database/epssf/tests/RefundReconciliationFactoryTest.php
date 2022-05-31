<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefundReconciliationFactoryTest extends TestCase
{
    protected $factory;

    public function setUp()
    {
    	parent::setUp();
    	$this->factory = $this->app->make('Ceb\Factories\RefundReconciliationFactory');
    }

    public function test_reconciliation()
    {
    	$this->assertEquals($this->factory->reconcile(),null);
    }
}

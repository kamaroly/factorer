<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionCorrectionControllerTest extends TestCase
{
	protected $controller;

	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('\Ceb\Http\Controllers\ContributionCorrectionController');
	}

	public function test_user_can_browser_index()
	{
        $controller = $this->controller->index();
        $this->assertEquals($controller->getName(),'corrections.contributions.find-transaction');
	}

	

	public function test_user_can_reload_corrections()
	{
		Input::merge(['transactionid' => 9996]);
        $controller = $this->controller->show();
        $this->assertEquals($controller->getName(),'corrections.contributions.index');
	}
}

<?php

use Ceb\Models\Loan;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoanCorrectionControllerTest extends TestCase
{
	protected $controller;
	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('\Ceb\Http\Controllers\LoanCorrectionController');
	}
    public function test_index_renders_correct_view()
    {
        $indexMethod = $this->controller->index();
        $this->assertEquals($indexMethod->getName(),'corrections.loan.find-loan');
    }

    public function test_index_renders_show()
    {
		$loan                         = Loan::approved()->orderBy('created_at','desc')->first();
		Input::merge(['transactionid' => $loan->transactionid]);
		$indexMethod                  = $this->controller->show();
        $this->assertEquals($indexMethod->getName(),'corrections.loan.form');
    }

    public function test_loan_can_be_adjusted()
    {
		$loan    = Loan::approved()->orderBy('created_at','desc')->first();
		$request = request();
		
		$request->merge($loan->toArray());
		$results = $this->controller->update($request,$loan->transactionid);

		$this->seeInDatabase('approvals',['transactionid' => $loan->transactionid]);
        $this->assertEquals($results->getName(),'corrections.loan.find-loan');

    }
}

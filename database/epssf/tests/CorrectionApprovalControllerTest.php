<?php

use Ceb\Models\Approval;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CorrectionApprovalControllerTest extends TestCase
{
	protected $controller;

	public function setUp()
	{
		parent::setUp();
		$this->controller = $this->app->make('Ceb\Http\Controllers\CorrectionApprovalController');
	}

    public function test_logged_in_user_can_navigate_approvals()
    {
        $this->login()->visit(route('corrections.approvals'))
            ->click(trans('navigations.correction_approvals'))
            ->seePageIs(route('corrections.approvals'));
    }

    public function test_user_can_review_approval()
    {
		$approval = Approval::first();
		$method   = $this->controller->show($approval->id);
		$this->assertEquals ($method->getName(),'corrections.approvals.show');
    }
}

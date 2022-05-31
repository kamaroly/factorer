<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Ceb\Models\Loan;

class LoanCorrectionPageTest extends TestCase
{

    public function test_logged_in_user_can_load_loan_for_correction()
    {
    	$this->login()->visit(env('APP_URL').'/')
            ->see(trans('navigations.correction'))
            ->click(trans('navigations.loan_correction'))
            ->seePageIs(route('corrections.loan.index'));
    }

    public function test_authorized_user_can_load_loan_to_correct()
    {
	    $loan = Loan::approved()->orderBy('created_at','desc')->first();
    	$this->login()
    		 ->visit(route('corrections.loan.index'))
             ->type($loan->transactionid,'transactionid')
             ->press(trans('loans.find_loan'))
             ->seePageIs(route('corrections.loan.show').'?transactionid='.$loan->transactionid);
    }

    public function test_authorized_user_can_adjust_a_loan()
    {
        $loan = Loan::approved()->orderBy('created_at','desc')->first();
        $this->login()->visit(route('corrections.loan.show').'?transactionid='.$loan->transactionid)
                      ->press('adjust-button')
                      ->seePageIs(route('corrections.loan.update',$loan->transactionid))
                      ->see(trans('loan.adjusted_successfully_waiting_for_approval'));
    }

    public function test_logged_in_user_can_see_loan_correction_menu()
    {
	    $this->login()->visit(env('APP_URL').'/')
             ->see(trans('navigations.loan_correction'));
    }

    public function test_logged_in_user_can_see_refund_correction_menu()
    {
	    $this->login()->visit(env('APP_URL').'/')
            ->see(trans('navigations.refund_correction'));
    }

    public function test_logged_in_user_can_see_contribution_correction_menu()
    {
	    $this->login()->visit(env('APP_URL').'/')
            ->see(trans('navigations.loan_correction'));
    }

}

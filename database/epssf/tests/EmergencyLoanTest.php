<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmergencyLoanTest extends TestCase
{

    public function testLoggedInUserCanSeeEmergenciesOnLeftNav()
    {
        	$this->sentryUserBe('admin@admin.com');
	        $this->visit(env('APP_URL').'/')
	        	 ->see(trans('navigations.emergency_loan_members'));
    }

    public function testUserCanSeeEmergencieScreen()
    {
            $this->sentryUserBe('admin@admin.com');
            $this->visit(env('APP_URL').'/')
                 ->see(trans('navigations.emergency_loan_members'))
                 ->click(trans('navigations.emergency_loan_members'))
                 ->seePageIs(env('APP_URL').'/members?emergencies=true')
                 ->see(trans('member.member_with_emergency_loans'));
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RefundPageTest extends TestCase
{
    public function testUserCanRefundEmergency()
    {
	    $this->sentryUserBe('admin@admin.com');

        $this->visit(env('APP_URL').'/')
            ->click(trans('navigations.emergency_refund'))
            ->seePageIs(route('refunds.index').'?refund-loan-type=EMERGENCY_LOAN');

        $this->assertSessionHas('refund-loan-type','EMERGENCY_LOAN');
    }

    public function testUserCanRefundOthers()
    {
	    $this->sentryUserBe('admin@admin.com');
        $this->visit(env('APP_URL').'/')
            ->click(trans('navigations.other_refund'))
            ->seePageIs(route('refunds.index').'?refund-loan-type=NON_EMERGENCY_LOAN');

        $this->assertSessionHas('refund-loan-type','NON_EMERGENCY_LOAN');
    }
}

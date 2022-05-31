<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Ceb\Models\{User,Loan};

class RefundFactoryTest extends TestCase
{
    protected $factory;

    public function setUp()
    {
    	parent::setUp();

    	$this->factory = $this->app->make('Ceb\Factories\RefundFactory');
    }

    public function testSetRefundType()
    {
        $this->factory->setRefundType('refund_by_interets_annuels');
        $this->assertEquals($this->factory->getRefundType(),'refund_by_interets_annuels');

        $this->factory->removeRefundType();
        $this->assertEquals($this->factory->getRefundType(),null);
    }

    public function testSetRefundLoanType()
    {
        $this->factory->setRefundLoanType('NON_EMERGENCY_LOAN');
        $this->assertEquals($this->factory->getRefundLoanType(),'NON_EMERGENCY_LOAN');

        $this->factory->removeRefundLoanType();
        $this->assertEquals($this->factory->getRefundLoanType(),null);
    }

    public function testRefundCanBCompleted()
    {
        $this->sentryUserBe('admin@admin.com');
        $status = $this->factory->setMember(107);
        $this->assertTrue($status);
    }

    public function test_if_current_refund_is_emergency()
    {
        $this->factory->setRefundLoanType('EMERGENCY_LOAN');
        $this->assertTrue($this->factory->isEmergencyLoanRefund());
    }

    public function test_member_can_be_added_for_refund()
    {
        $results = $this->factory->setMember(107);
        $this->assertTrue($results);
    }

    // public function test_emergencies_return_with_monthly_payment()
    // {
    //     dd(Loan::select('adhersion_id')->isNotPaidUmergency()->get()->pluck('adhersion_id')->count());
    // }
}

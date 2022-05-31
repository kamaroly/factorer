<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContributionFactoryTest extends TestCase
{
    protected $contributionFactory;

    public function setUp()
    {
        parent::setUp();

        $this->contributionFactory = $this->app->make('Ceb\Factories\ContributionFactory');
    }
    
    public function testSetRecurrenceToSession()
    {
        $this->contributionFactory->setRecurrence('yearly');
        $this->assertSessionHas('contribution.recurrence','yearly');
        // Test monthly
        $this->contributionFactory->setRecurrence('monthly');
        $this->assertSessionHas('contribution.recurrence','monthly');
    }

    public function testGetRecurrenceToSession()
    {
        $this->contributionFactory->setRecurrence('yearly');
        $this->assertSessionHas('contribution.recurrence','yearly');
        
        $recurrence = $this->contributionFactory->getRecurrence();
        $this->assertEquals($recurrence,'yearly');

        $this->contributionFactory->setRecurrence('monthly');
        $recurrence = $this->contributionFactory->getRecurrence('monthly');
        $this->assertEquals($recurrence,'monthly');
    }

    public function testSetContributionType()
    {
        $this->contributionFactory->setContributionType('ANNUAL_INTEREST');
        $this->assertSessionHas('contributions-type','ANNUAL_INTEREST');
        
        $contributionType = $this->contributionFactory->getContributionType();
        $this->assertEquals($contributionType,'ANNUAL_INTEREST');
    }

    public function testSetInterestYear()
    {
        $this->contributionFactory->setInterestYear('2014');
        $this->assertSessionHas('interest-year','2014');
        
        $year = $this->contributionFactory->getInterestYear();
        $this->assertEquals($year,'2014');
    }

    public function testWordingIsAnnualInterestForAnnualInterest()
    {
        $this->contributionFactory->setContributionType('ANNUAL_INTEREST');
        $this->contributionFactory->setInterestYear('2014');
        
        $wording = $this->contributionFactory->getWording();
        $this->assertEquals($wording,'ANNUAL_INTEREST_2014');
    }
    public function testAnnualWithdraw()
    {
        $this->contributionFactory->setContributionType('BULK_WITHDRAW');
        $this->contributionFactory->setInterestYear('2013');
        
        $wording = $this->contributionFactory->getWording();
        $this->assertEquals($wording,'BULK_WITHDRAW_2013');
    }

    public function testSetTransactionType()
    {
        $this->contributionFactory->setTransactionType('withdrawal');
        $this->assertSessionHas('transaction-type','withdrawal');
        
        $transactionType = $this->contributionFactory->getTransactionType();
        $this->assertEquals($transactionType,'withdrawal');
    }

    public function testUserCanUploadBulkWithdraw()
    {
        $members = collect(["20070025","20070026","20070028",]);

        $this->contributionFactory->setInterestYear('2013');
        $this->contributionFactory->setWithdrawMembers($members);

        $this->assertEquals($this->contributionFactory->getConstributions()->count(),0);
    }
}
